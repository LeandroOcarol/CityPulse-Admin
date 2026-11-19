<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminNotificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATED ADMINISTRATOR
    |--------------------------------------------------------------------------
    */

    private function adminId(): ?string
    {
        $admin = session('admin_user', []);

        $id = data_get($admin, 'id');

        $role = strtolower(
            (string) data_get($admin, 'role', '')
        );

        if (
            $role !== 'admin' ||
            !is_string($id) ||
            !Str::isUuid($id)
        ) {
            return null;
        }

        return $id;
    }

    /*
    |--------------------------------------------------------------------------
    | RETRIEVE NOTIFICATIONS
    |--------------------------------------------------------------------------
    |
    | Fast read-only endpoint.
    | Does not synchronize source tables.
    |
    */

    public function index(): JsonResponse
    {
        $adminId = $this->adminId();

        if (!$adminId) {
            return response()->json([
                'message' => 'Unauthorized administrator.',
            ], 401);
        }

        try {
            $unreadCount = DB::table('admin_notifications')
                ->where('admin_id', $adminId)
                ->whereNull('read_at')
                ->count();

            $notifications = DB::table('admin_notifications')
                ->where('admin_id', $adminId)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->limit(30)
                ->get([
                    'id',
                    'source_type',
                    'title',
                    'message',
                    'target_url',
                    'created_at',
                    'read_at',
                ])
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->source_type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'url' => $notification->target_url,
                        'created_at' => $notification->created_at,
                        'is_read' => $notification->read_at !== null,
                    ];
                })
                ->values();

            return response()
                ->json([
                    'unread_count' => $unreadCount,
                    'notifications' => $notifications,
                ])
                ->header('Cache-Control', 'no-store');

        } catch (\Throwable $e) {
            Log::error(
                'CityPulse notification retrieval failed',
                ['exception' => $e]
            );

            return response()->json([
                'message' => 'Unable to load notifications.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SYNCHRONIZE NEW ACTIVITY
    |--------------------------------------------------------------------------
    */

    public function sync(): JsonResponse
    {
        $adminId = $this->adminId();

        if (!$adminId) {
            return response()->json([
                'message' => 'Unauthorized administrator.',
            ], 401);
        }

        try {
            $insertedCount = $this->synchronizeNotifications(
                $adminId
            );

            $unreadCount = DB::table('admin_notifications')
                ->where('admin_id', $adminId)
                ->whereNull('read_at')
                ->count();

            return response()
                ->json([
                    'success' => true,
                    'inserted_count' => $insertedCount,
                    'unread_count' => $unreadCount,
                ])
                ->header('Cache-Control', 'no-store');

        } catch (\Throwable $e) {
            Log::error(
                'CityPulse notification synchronization failed',
                ['exception' => $e]
            );

            return response()->json([
                'message' => 'Unable to synchronize notifications.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | OPTIMIZED NOTIFICATION SYNCHRONIZATION
    |--------------------------------------------------------------------------
    |
    | 1. Preserve the administrator's original baseline.
    | 2. Search three event sources in one SQL request.
    | 3. Exclude events with existing notifications.
    | 4. Insert up to 100 new notifications per request.
    |
    | Existing incidents and notifications are preserved.
    |
    */

    private function synchronizeNotifications(
        string $adminId
    ): int {
        return DB::transaction(function () use ($adminId) {

            /*
            |--------------------------------------------------------------------------
            | INITIALIZE WATCHER
            |--------------------------------------------------------------------------
            */

            $created = DB::table(
                'admin_notification_watchers'
            )->insertOrIgnore([
                'admin_id' => $adminId,
                'started_at' => DB::raw('CURRENT_TIMESTAMP'),
            ]);

            if ($created > 0) {
                return 0;
            }

            $watcher = DB::table(
                'admin_notification_watchers'
            )
                ->where('admin_id', $adminId)
                ->first();

            if (!$watcher) {
                throw new \RuntimeException(
                    'Notification watcher could not be found.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | FETCH UNSENT EVENTS
            |--------------------------------------------------------------------------
            |
            | All source queries use the same watcher baseline.
            | Each source is checked against existing notifications.
            |
            | Bound parameters prevent SQL injection.
            |
            */

            $sql = <<<'SQL'
                SELECT
                    events.source_type,
                    events.source_id,
                    events.detail,
                    events.created_at
                FROM (
                    SELECT
                        'incident' AS source_type,
                        i.incident_id::text AS source_id,
                        COALESCE(i.title, '') AS detail,
                        i.created_at
                    FROM public.incidents AS i
                    WHERE i.created_at > ?::timestamptz
                      AND NOT EXISTS (
                          SELECT 1
                          FROM public.admin_notifications AS n
                          WHERE n.admin_id = ?::uuid
                            AND n.source_type = 'incident'
                            AND n.source_id = i.incident_id::text
                      )

                    UNION ALL

                    SELECT
                        'tourist_registration' AS source_type,
                        p.id::text AS source_id,
                        TRIM(
                            CONCAT_WS(
                                ' ',
                                NULLIF(p.first_name, ''),
                                NULLIF(p.last_name, '')
                            )
                        ) AS detail,
                        p.created_at
                    FROM public.profiles AS p
                    WHERE p.role = 'tourist'
                      AND p.created_at > ?::timestamptz
                      AND NOT EXISTS (
                          SELECT 1
                          FROM public.admin_notifications AS n
                          WHERE n.admin_id = ?::uuid
                            AND n.source_type = 'tourist_registration'
                            AND n.source_id = p.id::text
                      )

                    UNION ALL

                    SELECT
                        'review' AS source_type,
                        r.review_id::text AS source_id,
                        COALESCE(r.rating::text, '') AS detail,
                        r.created_at
                    FROM public.reviews AS r
                    WHERE r.created_at > ?::timestamptz
                      AND NOT EXISTS (
                          SELECT 1
                          FROM public.admin_notifications AS n
                          WHERE n.admin_id = ?::uuid
                            AND n.source_type = 'review'
                            AND n.source_id = r.review_id::text
                      )
                ) AS events
                ORDER BY
                    events.created_at ASC,
                    events.source_type ASC,
                    events.source_id ASC
                LIMIT 100
            SQL;

            $events = DB::select($sql, [
                $watcher->started_at,
                $adminId,

                $watcher->started_at,
                $adminId,

                $watcher->started_at,
                $adminId,
            ]);

            if (empty($events)) {
                return 0;
            }

            /*
            |--------------------------------------------------------------------------
            | PREPARE NOTIFICATION BATCH
            |--------------------------------------------------------------------------
            */

            $notifications = [];

            foreach ($events as $event) {

                $sourceType = (string) $event->source_type;
                $sourceId = (string) $event->source_id;
                $detail = trim((string) $event->detail);

                $title = '';
                $message = '';
                $targetUrl = '';

                if ($sourceType === 'incident') {

                    $title = 'New Incident Report';

                    $message = $detail !== ''
                        ? 'New incident submitted: ' . $detail
                        : 'A tourist submitted a new incident report.';

                    $targetUrl = route(
                        'admin.incident-reports.show',
                        ['id' => $sourceId],
                        false
                    );

                } elseif ($sourceType === 'tourist_registration') {

                    $title = 'New Tourist Registration';

                    $message = $detail !== ''
                        ? $detail . ' registered in CityPulse.'
                        : 'A new tourist registered in CityPulse.';

                    $targetUrl = route(
                        'admin.tourist-accounts.show',
                        ['id' => $sourceId],
                        false
                    );

                } elseif ($sourceType === 'review') {

                    $title = 'New Rating & Feedback';

                    $message = $detail !== ''
                        ? 'A tourist submitted a ' .
                            $detail . '-star rating.'
                        : 'A tourist submitted new feedback.';

                    $targetUrl = route(
                        'admin.ratings-feedback.show',
                        ['id' => $sourceId],
                        false
                    );

                } else {
                    continue;
                }

                $notifications[] = [
                    'admin_id' => $adminId,
                    'source_type' => $sourceType,
                    'source_id' => $sourceId,
                    'title' => $title,
                    'message' => $message,
                    'target_url' => $targetUrl,
                    'created_at' => $event->created_at,
                    'read_at' => null,
                ];
            }

            if (empty($notifications)) {
                return 0;
            }

            /*
            |--------------------------------------------------------------------------
            | BULK INSERT
            |--------------------------------------------------------------------------
            |
            | The existing unique database constraint prevents
            | duplicate notifications, including concurrent inserts.
            |
            */

            return DB::table('admin_notifications')
                ->insertOrIgnore($notifications);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | MARK ONE NOTIFICATION AS READ
    |--------------------------------------------------------------------------
    */

    public function markRead(
        Request $request,
        int $id
    ): JsonResponse {
        $adminId = $this->adminId();

        if (!$adminId) {
            return response()->json([
                'message' => 'Unauthorized administrator.',
            ], 401);
        }

        try {
            $notification = DB::table('admin_notifications')
                ->where('id', $id)
                ->where('admin_id', $adminId)
                ->first();

            if (!$notification) {
                return response()->json([
                    'message' => 'Notification not found.',
                ], 404);
            }

            DB::table('admin_notifications')
                ->where('id', $id)
                ->where('admin_id', $adminId)
                ->whereNull('read_at')
                ->update([
                    'read_at' => DB::raw('CURRENT_TIMESTAMP'),
                ]);

            return response()->json([
                'success' => true,
                'url' => $notification->target_url,
            ]);

        } catch (\Throwable $e) {
            Log::error(
                'CityPulse mark notification failed',
                ['exception' => $e]
            );

            return response()->json([
                'message' => 'Unable to update notification.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MARK ALL NOTIFICATIONS AS READ
    |--------------------------------------------------------------------------
    */

    public function markAllRead(Request $request): JsonResponse
    {
        $adminId = $this->adminId();

        if (!$adminId) {
            return response()->json([
                'message' => 'Unauthorized administrator.',
            ], 401);
        }

        try {
            DB::table('admin_notifications')
                ->where('admin_id', $adminId)
                ->whereNull('read_at')
                ->update([
                    'read_at' => DB::raw('CURRENT_TIMESTAMP'),
                ]);

            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);

        } catch (\Throwable $e) {
            Log::error(
                'CityPulse mark all notifications failed',
                ['exception' => $e]
            );

            return response()->json([
                'message' => 'Unable to update notifications.',
            ], 500);
        }
    }
}
