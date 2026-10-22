<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SafetyAlertController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Safety Alerts Directory
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $search = trim($request->get('search', ''));
        $level = trim($request->get('level', 'all'));
        $status = trim($request->get('status', 'all'));
        $zone = trim($request->get('zone', 'all'));

        $query = DB::table('safety_alerts as sa')
            ->leftJoin(
                'geofence_zones as gz',
                'gz.zone_id',
                '=',
                'sa.zone_id'
            )
            ->leftJoin(
                'profiles as p',
                'p.id',
                '=',
                'sa.created_by'
            )
            ->select(
                'sa.alert_id',
                'sa.zone_id',
                'sa.title',
                'sa.message',
                'sa.alert_level',
                'sa.starts_at',
                'sa.ends_at',
                'sa.is_active',
                'sa.created_by',
                'sa.created_at',
                'gz.zone_name',
                'gz.zone_type',
                'p.first_name as creator_first_name',
                'p.last_name as creator_last_name'
            );

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where(
                    'sa.title',
                    'ilike',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'sa.message',
                    'ilike',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'gz.zone_name',
                    'ilike',
                    '%' . $search . '%'
                );
            });
        }

        if ($level !== 'all') {
            $query->where(
                'sa.alert_level',
                $level
            );
        }

        if ($status === 'active') {
            $query->where(
                'sa.is_active',
                true
            );
        }

        if ($status === 'inactive') {
            $query->where(
                'sa.is_active',
                false
            );
        }

        if ($zone !== 'all') {
            $query->where(
                'sa.zone_id',
                $zone
            );
        }

        $alerts = $query
            ->orderByDesc('sa.is_active')
            ->orderByDesc('sa.created_at')
            ->paginate(8)
            ->withQueryString();

        $totalAlerts = DB::table('safety_alerts')
            ->count();

        $activeAlerts = DB::table('safety_alerts')
            ->where('is_active', true)
            ->count();

        $inactiveAlerts = DB::table('safety_alerts')
            ->where('is_active', false)
            ->count();

        $emergencyAlerts = DB::table('safety_alerts')
            ->where('alert_level', 'Emergency')
            ->count();

        $zones = $this->getZones();

        return view(
            'admin.safety-alerts.index',
            [
                'alerts' => $alerts,
                'totalAlerts' => $totalAlerts,
                'activeAlerts' => $activeAlerts,
                'inactiveAlerts' => $inactiveAlerts,
                'emergencyAlerts' => $emergencyAlerts,
                'zones' => $zones,
                'search' => $search,
                'level' => $level,
                'status' => $status,
                'zone' => $zone,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Add Safety Alert
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        return view(
            'admin.safety-alerts.create',
            [
                'zones' => $this->getZones(),
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store Safety Alert
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $validated = $this->validateAlert($request);

        $createdBy = $this->getAdminUuid();

        DB::table('safety_alerts')
            ->insert([
                'zone_id' => $validated['zone_id'],
                'title' => $validated['title'],
                'message' => $validated['message'],
                'alert_level' => $validated['alert_level'],
                'starts_at' => $this->toUtc(
                    $validated['starts_at']
                ),
                'ends_at' => !empty($validated['ends_at'])
                    ? $this->toUtc($validated['ends_at'])
                    : null,
                'is_active' => true,
                'created_by' => $createdBy,
                'created_at' => now(),
            ]);

        return redirect()
            ->route('admin.safety-alerts.index')
            ->with(
                'success',
                'Safety alert added successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Safety Alert Details
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $alert = $this->getAlert($id);

        abort_if(!$alert, 404);

        return view(
            'admin.safety-alerts.show',
            [
                'alert' => $alert,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Safety Alert
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $alert = $this->getAlert($id);

        abort_if(!$alert, 404);

        return view(
            'admin.safety-alerts.edit',
            [
                'alert' => $alert,
                'zones' => $this->getZones(),
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Safety Alert
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $exists = DB::table('safety_alerts')
            ->where('alert_id', $id)
            ->exists();

        abort_if(!$exists, 404);

        $validated = $this->validateAlert($request);

        DB::table('safety_alerts')
            ->where('alert_id', $id)
            ->update([
                'zone_id' => $validated['zone_id'],
                'title' => $validated['title'],
                'message' => $validated['message'],
                'alert_level' => $validated['alert_level'],
                'starts_at' => $this->toUtc(
                    $validated['starts_at']
                ),
                'ends_at' => !empty($validated['ends_at'])
                    ? $this->toUtc($validated['ends_at'])
                    : null,
            ]);

        return redirect()
            ->route(
                'admin.safety-alerts.show',
                $id
            )
            ->with(
                'success',
                'Safety alert updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Activate / Deactivate
    |--------------------------------------------------------------------------
    */

    public function updateStatus(Request $request, $id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $alert = DB::table('safety_alerts')
            ->where('alert_id', $id)
            ->first();

        abort_if(!$alert, 404);

        $validated = $request->validate([
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        DB::table('safety_alerts')
            ->where('alert_id', $id)
            ->update([
                'is_active' => (bool) $validated['is_active'],
            ]);

        $message = $validated['is_active']
            ? 'Safety alert reactivated successfully.'
            : 'Safety alert deactivated successfully.';

        return back()->with(
            'success',
            $message
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Safety Alert
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $alert = DB::table('safety_alerts')
            ->where('alert_id', $id)
            ->first();

        abort_if(!$alert, 404);

        DB::table('safety_alerts')
            ->where('alert_id', $id)
            ->delete();

        return redirect()
            ->route('admin.safety-alerts.index')
            ->with(
                'success',
                'Safety alert deleted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Shared Alert Query
    |--------------------------------------------------------------------------
    */

    private function getAlert($id)
    {
        return DB::table('safety_alerts as sa')
            ->leftJoin(
                'geofence_zones as gz',
                'gz.zone_id',
                '=',
                'sa.zone_id'
            )
            ->leftJoin(
                'profiles as p',
                'p.id',
                '=',
                'sa.created_by'
            )
            ->where('sa.alert_id', $id)
            ->select(
                'sa.alert_id',
                'sa.zone_id',
                'sa.title',
                'sa.message',
                'sa.alert_level',
                'sa.starts_at',
                'sa.ends_at',
                'sa.is_active',
                'sa.created_by',
                'sa.created_at',
                'gz.zone_name',
                'gz.zone_type',
                'gz.radius_meters',
                'gz.alert_message',
                'p.first_name as creator_first_name',
                'p.last_name as creator_last_name'
            )
            ->first();
    }


    /*
    |--------------------------------------------------------------------------
    | Geofence Zones
    |--------------------------------------------------------------------------
    */

    private function getZones()
    {
        return DB::table('geofence_zones')
            ->orderBy('zone_name')
            ->get([
                'zone_id',
                'zone_name',
                'zone_type',
            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    private function validateAlert(Request $request): array
    {
        return $request->validate([
            'zone_id' => [
                'required',
                'integer',
                'exists:geofence_zones,zone_id',
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'message' => [
                'required',
                'string',
            ],
            'alert_level' => [
                'required',
                'in:Low,Medium,High,Emergency',
            ],
            'starts_at' => [
                'required',
                'date',
            ],
            'ends_at' => [
                'nullable',
                'date',
                'after:starts_at',
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Logged-in Admin UUID
    |--------------------------------------------------------------------------
    */

    private function getAdminUuid(): ?string
    {
        $admin = session('admin_user');

        $id = null;

        if (is_array($admin)) {
            $id = $admin['id'] ?? null;
        }

        if (is_object($admin)) {
            $id = $admin->id ?? null;
        }

        if (!$this->isUuid($id)) {
            return null;
        }

        $exists = DB::table('profiles')
            ->where('id', $id)
            ->exists();

        return $exists
            ? $id
            : null;
    }


    /*
    |--------------------------------------------------------------------------
    | Date Conversion
    |--------------------------------------------------------------------------
    */

    private function toUtc($value)
    {
        return Carbon::parse(
            $value,
            'Asia/Manila'
        )->utc();
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