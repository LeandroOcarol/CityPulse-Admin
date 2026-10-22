<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CrimeRecordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Crime Records Directory
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $search = trim($request->get('search', ''));
        $severity = trim($request->get('severity', 'all'));
        $type = trim($request->get('type', 'all'));
        $zone = trim($request->get('zone', 'all'));

        $query = DB::table('crime_data as c')
            ->leftJoin(
                'incident_types as it',
                'it.type_id',
                '=',
                'c.type_id'
            )
            ->leftJoin(
                'geofence_zones as gz',
                'gz.zone_id',
                '=',
                'c.zone_id'
            )
            ->select(
                'c.crime_id',
                'c.type_id',
                'c.zone_id',
                'c.crime_date',
                'c.severity',
                'c.crime_count',
                'c.source',
                'c.created_at',
                'it.incident_name',
                'it.report_category',
                'gz.zone_name',
                'gz.zone_type'
            )
            ->selectRaw(
                'CASE
                    WHEN c.location IS NOT NULL
                    THEN extensions.ST_Y(c.location::extensions.geometry)
                    ELSE NULL
                END AS latitude'
            )
            ->selectRaw(
                'CASE
                    WHEN c.location IS NOT NULL
                    THEN extensions.ST_X(c.location::extensions.geometry)
                    ELSE NULL
                END AS longitude'
            );

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where(
                    'it.incident_name',
                    'ilike',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'it.report_category',
                    'ilike',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'c.source',
                    'ilike',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'c.severity',
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

        if ($severity !== 'all') {
            $query->where('c.severity', $severity);
        }

        if ($type !== 'all') {
            $query->where('c.type_id', $type);
        }

        if ($zone !== 'all') {
            if ($zone === 'unassigned') {
                $query->whereNull('c.zone_id');
            } else {
                $query->where('c.zone_id', $zone);
            }
        }

        $crimeRecords = $query
            ->orderByDesc('c.crime_date')
            ->orderByDesc('c.crime_id')
            ->paginate(8)
            ->withQueryString();

        $totalRecords = DB::table('crime_data')
            ->count();

        $totalCrimeCount = DB::table('crime_data')
            ->sum('crime_count');

        $severities = DB::table('crime_data')
            ->whereNotNull('severity')
            ->where('severity', '!=', '')
            ->distinct()
            ->orderBy('severity')
            ->pluck('severity');

        $crimeTypes = $this->getCrimeTypes();
        $zones = $this->getZones();

        return view(
            'admin.crime-records.index',
            [
                'crimeRecords' => $crimeRecords,
                'totalRecords' => $totalRecords,
                'totalCrimeCount' => $totalCrimeCount,
                'severities' => $severities,
                'crimeTypes' => $crimeTypes,
                'zones' => $zones,
                'search' => $search,
                'severity' => $severity,
                'type' => $type,
                'zone' => $zone,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Add Crime Record
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        return view(
            'admin.crime-records.create',
            [
                'crimeTypes' => $this->getCrimeTypes(),
                'zones' => $this->getZones(),
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store Crime Record
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $validated = $this->validateCrimeRecord($request);

        DB::statement(
            '
                INSERT INTO crime_data (
                    type_id,
                    zone_id,
                    location,
                    crime_date,
                    severity,
                    crime_count,
                    source,
                    created_at
                )
                VALUES (
                    ?,
                    ?,
                    extensions.ST_SetSRID(
                        extensions.ST_MakePoint(?, ?),
                        4326
                    )::extensions.geography,
                    ?,
                    ?,
                    ?,
                    ?,
                    NOW()
                )
            ',
            [
                $validated['type_id'],
                $validated['zone_id'] ?: null,
                $validated['longitude'],
                $validated['latitude'],
                $validated['crime_date'],
                $validated['severity'],
                $validated['crime_count'],
                $validated['source'],
            ]
        );

        return redirect()
            ->route('admin.crime-records.index')
            ->with(
                'success',
                'Crime record added successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Crime Record Details
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $record = $this->getCrimeRecord($id);

        abort_if(!$record, 404);

        return view(
            'admin.crime-records.show',
            [
                'record' => $record,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Crime Record
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $record = $this->getCrimeRecord($id);

        abort_if(!$record, 404);

        return view(
            'admin.crime-records.edit',
            [
                'record' => $record,
                'crimeTypes' => $this->getCrimeTypes(),
                'zones' => $this->getZones(),
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Crime Record
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $exists = DB::table('crime_data')
            ->where('crime_id', $id)
            ->exists();

        abort_if(!$exists, 404);

        $validated = $this->validateCrimeRecord($request);

        DB::statement(
            '
                UPDATE crime_data

                SET
                    type_id = ?,
                    zone_id = ?,
                    location =
                        extensions.ST_SetSRID(
                            extensions.ST_MakePoint(?, ?),
                            4326
                        )::extensions.geography,
                    crime_date = ?,
                    severity = ?,
                    crime_count = ?,
                    source = ?

                WHERE crime_id = ?
            ',
            [
                $validated['type_id'],
                $validated['zone_id'] ?: null,
                $validated['longitude'],
                $validated['latitude'],
                $validated['crime_date'],
                $validated['severity'],
                $validated['crime_count'],
                $validated['source'],
                $id,
            ]
        );

        return redirect()
            ->route(
                'admin.crime-records.show',
                $id
            )
            ->with(
                'success',
                'Crime record updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Crime Record
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $record = DB::table('crime_data')
            ->where('crime_id', $id)
            ->first();

        abort_if(!$record, 404);

        DB::table('crime_data')
            ->where('crime_id', $id)
            ->delete();

        return redirect()
            ->route('admin.crime-records.index')
            ->with(
                'success',
                'Crime record deleted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Shared Crime Record Query
    |--------------------------------------------------------------------------
    */

    private function getCrimeRecord($id)
    {
        return DB::table('crime_data as c')
            ->leftJoin(
                'incident_types as it',
                'it.type_id',
                '=',
                'c.type_id'
            )
            ->leftJoin(
                'geofence_zones as gz',
                'gz.zone_id',
                '=',
                'c.zone_id'
            )
            ->where('c.crime_id', $id)
            ->select(
                'c.crime_id',
                'c.type_id',
                'c.zone_id',
                'c.crime_date',
                'c.severity',
                'c.crime_count',
                'c.source',
                'c.created_at',
                'it.incident_name',
                'it.report_category',
                'gz.zone_name',
                'gz.zone_type',
                'gz.radius_meters',
                'gz.alert_message'
            )
            ->selectRaw(
                'CASE
                    WHEN c.location IS NOT NULL
                    THEN extensions.ST_Y(c.location::extensions.geometry)
                    ELSE NULL
                END AS latitude'
            )
            ->selectRaw(
                'CASE
                    WHEN c.location IS NOT NULL
                    THEN extensions.ST_X(c.location::extensions.geometry)
                    ELSE NULL
                END AS longitude'
            )
            ->first();
    }


    /*
    |--------------------------------------------------------------------------
    | Shared Form Data
    |--------------------------------------------------------------------------
    */

    private function getCrimeTypes()
    {
        return DB::table('incident_types')
            ->where('is_active', true)
            ->orderBy('incident_name')
            ->get([
                'type_id',
                'incident_name',
            ]);
    }

    private function getZones()
    {
        return DB::table('geofence_zones')
            ->orderBy('zone_name')
            ->get([
                'zone_id',
                'zone_name',
            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    private function validateCrimeRecord(Request $request): array
    {
        return $request->validate([
            'type_id' => [
                'required',
                'integer',
                'exists:incident_types,type_id',
            ],
            'zone_id' => [
                'nullable',
                'integer',
                'exists:geofence_zones,zone_id',
            ],
            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],
            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],
            'crime_date' => [
                'required',
                'date',
            ],
            'severity' => [
                'required',
                'in:Low,Medium,High',
            ],
            'crime_count' => [
                'required',
                'integer',
                'min:1',
            ],
            'source' => [
                'required',
                'string',
                'max:255',
            ],
        ]);
    }
}