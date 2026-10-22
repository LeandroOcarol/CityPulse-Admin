<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncidentReportController extends Controller
{
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
                $q->where(
                    'i.title',
                    'ilike',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'i.description',
                    'ilike',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'it.incident_name',
                    'ilike',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'i.other_type',
                    'ilike',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'p.first_name',
                    'ilike',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'p.last_name',
                    'ilike',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'au.email',
                    'ilike',
                    '%' . $search . '%'
                );
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

        $totalIncidents = DB::table('incidents')
            ->count();

        $pendingIncidents = DB::table('incidents')
            ->where('status', 'Pending')
            ->count();

        $incidentTypes = DB::table('incident_types')
            ->orderBy('incident_name')
            ->get([
                'type_id',
                'incident_name',
            ]);

        return view(
            'admin.incident-reports.index',
            [
                'incidents' => $incidents,
                'totalIncidents' => $totalIncidents,
                'pendingIncidents' => $pendingIncidents,
                'incidentTypes' => $incidentTypes,
                'search' => $search,
                'status' => $status,
                'type' => $type,
            ]
        );
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

        return view(
            'admin.incident-reports.show',
            [
                'incident' => $incident,
            ]
        );
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
            ->route(
                'admin.incident-reports.show',
                $id
            )
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