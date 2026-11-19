<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IncidentReportController extends Controller
{
    private const INCIDENT_IMAGE_BUCKET = 'incident-images';

    private const INCIDENT_IMAGE_MAX_BYTES = 8388608;

    /*
    |--------------------------------------------------------------------------
    | Incident Reports Directory
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $search = trim($request->get('search', ''));
        $status = trim($request->get('status', 'all'));
        $type = trim($request->get('type', 'all'));

        $query = DB::table('incidents as i')
            ->leftJoin(
                'incident_types as it',
                'it.type_id',
                '=',
                'i.type_id'
            )
            ->leftJoin(
                'profiles as p',
                'p.id',
                '=',
                'i.user_id'
            )
            ->leftJoin(
                'auth.users as au',
                'au.id',
                '=',
                'i.user_id'
            )
            ->select(
                'i.incident_id',
                'i.user_id',
                'i.type_id',
                'i.zone_id',
                'i.other_type',
                'i.title',
                'i.description',
                'i.image_url',
                'i.status',
                'i.reviewed_at',
                'i.reviewed_by',
                'i.created_at',
                'it.incident_name',
                'it.report_category',
                'p.first_name',
                'p.last_name',
                'au.email'
            )
            ->selectRaw(
                'CASE
                    WHEN i.location IS NOT NULL
                    THEN extensions.ST_Y(i.location::extensions.geometry)
                    ELSE NULL
                END AS latitude'
            )
            ->selectRaw(
                'CASE
                    WHEN i.location IS NOT NULL
                    THEN extensions.ST_X(i.location::extensions.geometry)
                    ELSE NULL
                END AS longitude'
            );

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('i.title', 'ilike', '%' . $search . '%')
                    ->orWhere('i.description', 'ilike', '%' . $search . '%')
                    ->orWhere('it.incident_name', 'ilike', '%' . $search . '%')
                    ->orWhere('i.other_type', 'ilike', '%' . $search . '%')
                    ->orWhere('p.first_name', 'ilike', '%' . $search . '%')
                    ->orWhere('p.last_name', 'ilike', '%' . $search . '%')
                    ->orWhere('au.email', 'ilike', '%' . $search . '%');
            });
        }

        if ($status !== 'all') {
            $query->where('i.status', $status);
        }

        if ($type !== 'all') {
            $query->where('i.type_id', $type);
        }

        $incidents = $query
            ->orderByDesc('i.created_at')
            ->paginate(8)
            ->withQueryString();

        $totalIncidents = DB::table('incidents')->count();

        $pendingIncidents = DB::table('incidents')
            ->where('status', 'Pending')
            ->count();

        $incidentTypes = DB::table('incident_types')
            ->orderBy('incident_name')
            ->get(['type_id', 'incident_name']);

        return view('admin.incident-reports.index', [
            'incidents' => $incidents,
            'totalIncidents' => $totalIncidents,
            'pendingIncidents' => $pendingIncidents,
            'incidentTypes' => $incidentTypes,
            'search' => $search,
            'status' => $status,
            'type' => $type,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Incident Report Details
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $incident = $this->getIncident($id);

        abort_if(!$incident, 404);

        return view('admin.incident-reports.show', [
            'incident' => $incident,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Private Incident Image
    |--------------------------------------------------------------------------
    */

    public function image($id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $admin = session('admin_user');

        if (
            !is_array($admin) ||
            strtolower((string) ($admin['role'] ?? '')) !== 'admin'
        ) {
            abort(403);
        }

        $incident = DB::table('incidents')
            ->where('incident_id', $id)
            ->first(['image_url']);

        abort_if(!$incident, 404);

        $objectKey = $incident->image_url ?? null;

        if ($objectKey === null || $objectKey === '') {
            abort(404);
        }

        if (!$this->isValidIncidentImageKey($objectKey)) {
            Log::warning('Incident image proxy: invalid storage key', [
                'incident_id' => $id,
            ]);

            abort(422);
        }

        $supabaseUrl = rtrim(
            (string) config('services.supabase.url'),
            '/'
        );

        $supabaseKey = (string) config('services.supabase.key');

        $accessToken = (string) session('supabase_access_token');

        if (
            $supabaseUrl === '' ||
            $supabaseKey === '' ||
            $accessToken === ''
        ) {
            Log::warning(
                'Incident image proxy: missing configuration or session',
                [
                    'incident_id' => $id,
                    'has_supabase_url' => $supabaseUrl !== '',
                    'has_supabase_key' => $supabaseKey !== '',
                    'has_access_token' => $accessToken !== '',
                ]
            );

            return $this->privateImageError(502);
        }

        /*
        |--------------------------------------------------------------------------
        | Supabase Authenticated Storage URL
        |--------------------------------------------------------------------------
        */

        $objectUrl = $supabaseUrl .
            '/storage/v1/object/authenticated/' .
            rawurlencode(self::INCIDENT_IMAGE_BUCKET) . '/' .
            collect(explode('/', $objectKey))
                ->map(fn (string $segment) => rawurlencode($segment))
                ->implode('/');

        /*
        |--------------------------------------------------------------------------
        | Request Image From Supabase
        |--------------------------------------------------------------------------
        */

        try {
            $storageResponse = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $accessToken,
            ])
                ->timeout(15)
                ->withOptions([
                    'allow_redirects' => false,

                    'on_headers' => static function (
                        \Psr\Http\Message\ResponseInterface $response
                    ): void {
                        $length = trim(
                            $response->getHeaderLine('Content-Length')
                        );

                        if ($length !== '' && ctype_digit($length)) {
                            $parsedLength = filter_var(
                                $length,
                                FILTER_VALIDATE_INT,
                                [
                                    'options' => [
                                        'min_range' => 0,
                                    ],
                                ]
                            );

                            if (
                                $parsedLength === false ||
                                $parsedLength > self::INCIDENT_IMAGE_MAX_BYTES
                            ) {
                                throw new \RuntimeException(
                                    'Incident image exceeds size limit.'
                                );
                            }
                        }
                    },

                    'progress' => static function (
                        $downloadTotal,
                        $downloadedBytes,
                        $uploadTotal,
                        $uploadedBytes
                    ): void {
                        if (
                            $downloadedBytes >
                            self::INCIDENT_IMAGE_MAX_BYTES
                        ) {
                            throw new \RuntimeException(
                                'Incident image exceeds size limit.'
                            );
                        }
                    },
                ])
                ->get($objectUrl);

        } catch (\Throwable $exception) {
            Log::warning(
                'Incident image proxy: Supabase request failed',
                [
                    'incident_id' => $id,
                    'exception_type' => get_class($exception),
                ]
            );

            return $this->privateImageError(502);
        }

        /*
        |--------------------------------------------------------------------------
        | Handle Supabase Error Responses
        |--------------------------------------------------------------------------
        */

        if ($storageResponse->status() === 404) {
            Log::warning(
                'Incident image proxy: storage object not found',
                [
                    'incident_id' => $id,
                    'upstream_status' => 404,
                ]
            );

            abort(404);
        }

        if (!$storageResponse->successful()) {
            $errorPayload = $storageResponse->json();

            $errorCode = null;

            if (is_array($errorPayload)) {
                $candidate = $errorPayload['code']
                    ?? $errorPayload['error_code']
                    ?? null;

                if (
                    is_string($candidate) &&
                    preg_match('/^[a-zA-Z0-9_-]{1,80}$/', $candidate)
                ) {
                    $errorCode = $candidate;
                }
            }

            Log::warning(
                'Incident image proxy: upstream response failed',
                [
                    'incident_id' => $id,
                    'upstream_status' => $storageResponse->status(),
                    'supabase_error_code' => $errorCode,
                    'upstream_content_type' => strtolower(trim(
                        explode(
                            ';',
                            (string) $storageResponse->header('Content-Type'),
                            2
                        )[0]
                    )),
                    'session_token_present' => $accessToken !== '',
                    'session_token_looks_like_jwt' =>
                        substr_count($accessToken, '.') === 2,
                ]
            );

            return $this->privateImageError(502);
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Image MIME Type
        |--------------------------------------------------------------------------
        */

        $declaredMime = strtolower(trim(
            explode(
                ';',
                (string) $storageResponse->header('Content-Type'),
                2
            )[0]
        ));

        if (
            !in_array(
                $declaredMime,
                $this->allowedIncidentImageMimes(),
                true
            )
        ) {
            Log::warning(
                'Incident image proxy: unsupported content type',
                [
                    'incident_id' => $id,
                    'declared_mime' => $declaredMime,
                ]
            );

            return $this->privateImageError(415);
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Content Length
        |--------------------------------------------------------------------------
        */

        $contentLength = trim(
            (string) $storageResponse->header('Content-Length')
        );

        if ($contentLength !== '') {
            if (!ctype_digit($contentLength)) {
                Log::warning(
                    'Incident image proxy: malformed content length',
                    [
                        'incident_id' => $id,
                    ]
                );

                return $this->privateImageError(413);
            }

            $parsedLength = filter_var(
                $contentLength,
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 0,
                    ],
                ]
            );

            if (
                $parsedLength === false ||
                $parsedLength > self::INCIDENT_IMAGE_MAX_BYTES
            ) {
                Log::warning(
                    'Incident image proxy: content length exceeds limit',
                    [
                        'incident_id' => $id,
                    ]
                );

                return $this->privateImageError(413);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Downloaded Image
        |--------------------------------------------------------------------------
        */

        $imageContents = $storageResponse->body();

        $receivedBytes = strlen($imageContents);

        Log::info(
            'Incident image proxy: download diagnostics',
            [
                'incident_id' => $id,
                'upstream_status' => $storageResponse->status(),
                'declared_mime' => $declaredMime,
                'declared_content_length' => $contentLength,
                'received_bytes' => $receivedBytes,
            ]
        );

        if ($receivedBytes > self::INCIDENT_IMAGE_MAX_BYTES) {
            Log::warning(
                'Incident image proxy: downloaded body exceeds limit',
                [
                    'incident_id' => $id,
                    'received_bytes' => $receivedBytes,
                ]
            );

            return $this->privateImageError(413);
        }

        if ($receivedBytes === 0) {
            Log::warning(
                'Incident image proxy: empty image body',
                [
                    'incident_id' => $id,
                ]
            );

            return $this->privateImageError(502);
        }

        if (
            $contentLength !== '' &&
            $receivedBytes !== (int) $contentLength
        ) {
            Log::warning(
                'Incident image proxy: incomplete image download',
                [
                    'incident_id' => $id,
                    'declared_content_length' => $contentLength,
                    'received_bytes' => $receivedBytes,
                ]
            );

            return $this->privateImageError(502);
        }

        $detectedMime = (new \finfo(FILEINFO_MIME_TYPE))
            ->buffer($imageContents);

        if ($detectedMime !== $declaredMime) {
            Log::warning(
                'Incident image proxy: MIME mismatch',
                [
                    'incident_id' => $id,
                    'declared_mime' => $declaredMime,
                    'detected_mime' => $detectedMime,
                ]
            );

            return $this->privateImageError(415);
        }

        /*
        |--------------------------------------------------------------------------
        | Return Image
        |--------------------------------------------------------------------------
        */

        Log::info(
            'Incident image proxy: image served successfully',
            [
                'incident_id' => $id,
                'mime' => $declaredMime,
                'received_bytes' => $receivedBytes,
            ]
        );

        return response($imageContents, 200, [
            'Content-Type' => $declaredMime,
            'Content-Length' => (string) $receivedBytes,
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Disposition' => 'inline',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Image Storage Key
    |--------------------------------------------------------------------------
    */

    private function isValidIncidentImageKey($objectKey): bool
    {
        return is_string($objectKey) &&
            strlen($objectKey) <= 256 &&
            preg_match(
                '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}\/[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}\.(?:jpg|png|webp)$/i',
                $objectKey
            ) === 1;
    }

    private function allowedIncidentImageMimes(): array
    {
        return [
            'image/jpeg',
            'image/png',
            'image/webp',
        ];
    }

    private function privateImageError(int $status)
    {
        return response('Incident image unavailable.', $status, [
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Approve / Reject Incident Report
    |--------------------------------------------------------------------------
    */

    public function updateStatus(Request $request, $id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $incident = DB::table('incidents')
            ->where('incident_id', $id)
            ->first();

        abort_if(!$incident, 404);

        $validated = $request->validate([
            'status' => [
                'required',
                'in:Approved,Rejected',
            ],
        ]);

        $admin = session('admin_user');

        $reviewedBy = null;

        if (
            is_array($admin) &&
            !empty($admin['id']) &&
            $this->isUuid($admin['id'])
        ) {
            $reviewedBy = $admin['id'];

        } elseif (
            is_object($admin) &&
            !empty($admin->id) &&
            $this->isUuid($admin->id)
        ) {
            $reviewedBy = $admin->id;
        }

        DB::table('incidents')
            ->where('incident_id', $id)
            ->update([
                'status' => $validated['status'],
                'reviewed_at' => now(),
                'reviewed_by' => $reviewedBy,
            ]);

        return redirect()
            ->route('admin.incident-reports.show', $id)
            ->with(
                'success',
                'Incident report status updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Incident Report
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $incident = DB::table('incidents')
            ->where('incident_id', $id)
            ->first();

        abort_if(!$incident, 404);

        DB::table('incidents')
            ->where('incident_id', $id)
            ->delete();

        return redirect()
            ->route('admin.incident-reports.index')
            ->with(
                'success',
                'Incident report deleted successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Shared Incident Query
    |--------------------------------------------------------------------------
    */

    private function getIncident($id)
    {
        return DB::table('incidents as i')
            ->leftJoin(
                'incident_types as it',
                'it.type_id',
                '=',
                'i.type_id'
            )
            ->leftJoin(
                'profiles as p',
                'p.id',
                '=',
                'i.user_id'
            )
            ->leftJoin(
                'auth.users as au',
                'au.id',
                '=',
                'i.user_id'
            )
            ->leftJoin(
                'geofence_zones as gz',
                'gz.zone_id',
                '=',
                'i.zone_id'
            )
            ->where('i.incident_id', $id)
            ->select(
                'i.incident_id',
                'i.user_id',
                'i.type_id',
                'i.zone_id',
                'i.other_type',
                'i.title',
                'i.description',
                'i.image_url',
                'i.status',
                'i.reviewed_at',
                'i.reviewed_by',
                'i.created_at',
                'it.incident_name',
                'it.report_category',
                'it.requires_photo',
                'p.first_name',
                'p.last_name',
                'p.phone_number',
                'p.profile_image_url',
                'au.email',
                'gz.zone_name',
                'gz.zone_type'
            )
            ->selectRaw(
                'CASE
                    WHEN i.location IS NOT NULL
                    THEN extensions.ST_Y(i.location::extensions.geometry)
                    ELSE NULL
                END AS latitude'
            )
            ->selectRaw(
                'CASE
                    WHEN i.location IS NOT NULL
                    THEN extensions.ST_X(i.location::extensions.geometry)
                    ELSE NULL
                END AS longitude'
            )
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | UUID Check
    |--------------------------------------------------------------------------
    */

    private function isUuid($value): bool
    {
        return is_string($value) &&
            preg_match(
                '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/',
                $value
            ) === 1;
    }
}
