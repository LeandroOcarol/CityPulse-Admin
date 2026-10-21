<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncidentReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Incident Report Directory
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
                'auth.users as u',
                'u.id',
                '=',
                'i.user_id'
            )
            ->select(
                'i.incident_id',
                'i.user_id',
                'i.type_id',
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
                'u.email'
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
                    'u.email',
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
            ->whereRaw('LOWER(status) = ?', ['pending'])
            ->count();

        $statuses = DB::table('incidents')
            ->whereNotNull('status')
            ->where('status', '!=', '')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        $incidentTypes = DB::table('incident_types')
            ->where('is_active', true)
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
                'statuses' => $statuses,
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
    | Update Incident Status
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

        $adminId = data_get(
            session('admin_user'),
            'id'
        );

        $updateData = [
            'status' => $validated['status'],
            'reviewed_at' => now(),
        ];

        if (
            is_string($adminId) &&
            $this->isUuid($adminId)
        ) {
            $updateData['reviewed_by'] = $adminId;
        }

        DB::table('incidents')
            ->where('incident_id', $id)
            ->update($updateData);

        return redirect()
            ->route(
                'admin.incident-reports.show',
                $id
            )
            ->with(
                'success',
                'Incident report status updated.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Incident Query
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
                'auth.users as u',
                'u.id',
                '=',
                'i.user_id'
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
                'u.email'
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
    | UUID Validation
    |--------------------------------------------------------------------------
    */

    private function isUuid(string $value): bool
    {
        return (bool) preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $value
        );
    }
}