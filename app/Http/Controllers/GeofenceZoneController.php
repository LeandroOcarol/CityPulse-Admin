<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GeofenceZoneController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SUPPORTED ADMIN ZONE TYPES
    |--------------------------------------------------------------------------
    */

    private const ZONE_TYPES = [
        'Safe',
        'Medium Risk',
        'High Risk',
        'Tourist',
    ];

    /*
    |--------------------------------------------------------------------------
    | AUTHORIZATION
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

    private function requireAdmin(): string
    {
        $adminId = $this->adminId();

        if (!$adminId) {
            abort(403, 'Administrator access required.');
        }

        return $adminId;
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    private function validatedZone(Request $request): array
    {
        return $request->validate([
            'zone_name' => [
                'required',
                'string',
                'max:150',
            ],

            'zone_type' => [
                'required',
                Rule::in(self::ZONE_TYPES),
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

            'radius_meters' => [
                'required',
                'integer',
                'between:1,50000',
            ],

            'alert_message' => [
                'required',
                'string',
                'max:1000',
            ],

            'tourist_id' => [
                'nullable',
                'integer',
                Rule::exists('tourist_spots', 'tourist_id'),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GEOFENCE DIRECTORY
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        if (!$this->adminId()) {
            return redirect()->route('admin.login');
        }

        $selectedType = $request->query('type', 'all');

        if (
            !is_string($selectedType) ||
            (
                $selectedType !== 'all' &&
                !in_array(
                    $selectedType,
                    self::ZONE_TYPES,
                    true
                )
            )
        ) {
            $selectedType = 'all';
        }

        $search = $request->query('search', '');

        if (!is_string($search)) {
            $search = '';
        }

        $search = trim(
            mb_substr($search, 0, 100)
        );

        /*
        |--------------------------------------------------------------------------
        | ZONE SUMMARY
        |--------------------------------------------------------------------------
        */

        $summaryRows = DB::table('geofence_zones')
            ->whereIn('zone_type', self::ZONE_TYPES)
            ->select('zone_type')
            ->selectRaw('COUNT(*) AS total')
            ->groupBy('zone_type')
            ->get();

        $counts = [
            'Safe' => 0,
            'Medium Risk' => 0,
            'High Risk' => 0,
            'Tourist' => 0,
        ];

        $totalZones = 0;

        foreach ($summaryRows as $row) {
            $count = (int) $row->total;

            if (array_key_exists($row->zone_type, $counts)) {
                $counts[$row->zone_type] = $count;
                $totalZones += $count;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ZONE DIRECTORY QUERY
        |--------------------------------------------------------------------------
        */

        $query = DB::table('geofence_zones as gz')
            ->leftJoin(
                'tourist_spots as ts',
                'ts.tourist_id',
                '=',
                'gz.tourist_id'
            )
            ->whereIn(
                'gz.zone_type',
                self::ZONE_TYPES
            );

        if ($selectedType !== 'all') {
            $query->where(
                'gz.zone_type',
                $selectedType
            );
        }

        if ($search !== '') {
            $escapedSearch = addcslashes(
                $search,
                '\\%_'
            );

            $searchTerm = '%' . $escapedSearch . '%';

            $query->where(
                function ($subQuery) use ($searchTerm) {
                    $subQuery
                        ->whereRaw(
                            "gz.zone_name ILIKE ? ESCAPE '\\'",
                            [$searchTerm]
                        )
                        ->orWhereRaw(
                            "ts.name ILIKE ? ESCAPE '\\'",
                            [$searchTerm]
                        );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | MAP COORDINATES
        |--------------------------------------------------------------------------
        */

        $zones = $query
            ->select(
                'gz.zone_id',
                'gz.zone_name',
                'gz.zone_type',
                'gz.radius_meters',
                'gz.alert_message',
                'ts.name as tourist_spot_name'
            )
            ->selectRaw(
                'extensions.ST_Y(gz.center::extensions.geometry) AS latitude'
            )
            ->selectRaw(
                'extensions.ST_X(gz.center::extensions.geometry) AS longitude'
            )
            ->orderBy('gz.zone_name')
            ->get()
            ->map(function ($zone) {
                return [
                    'zone_id' => $zone->zone_id,

                    'zone_name' => $zone->zone_name,

                    'zone_type' => $zone->zone_type,

                    'radius_meters' =>
                        (float) ($zone->radius_meters ?? 0),

                    'alert_message' => $zone->alert_message,

                    'tourist_spot_name' =>
                        $zone->tourist_spot_name,

                    'latitude' => $zone->latitude === null
                        ? null
                        : (float) $zone->latitude,

                    'longitude' => $zone->longitude === null
                        ? null
                        : (float) $zone->longitude,
                ];
            })
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | VALID MAPPED ZONES
        |--------------------------------------------------------------------------
        */

        $mappedZoneCount = 0;

        foreach ($zones as $zone) {
            $latitude = $zone['latitude'];
            $longitude = $zone['longitude'];
            $radius = $zone['radius_meters'];

            if (
                $latitude !== null &&
                $longitude !== null &&
                is_finite($latitude) &&
                is_finite($longitude) &&
                $latitude >= -90 &&
                $latitude <= 90 &&
                $longitude >= -180 &&
                $longitude <= 180 &&
                is_finite($radius) &&
                $radius > 0
            ) {
                $mappedZoneCount++;
            }
        }

        return view('admin.geofence-zones.index', [
            'zones' => $zones,
            'counts' => $counts,
            'totalZones' => $totalZones,
            'filteredZoneCount' => count($zones),
            'mappedZoneCount' => $mappedZoneCount,
            'selectedType' => $selectedType,
            'search' => $search,
            'zoneTypes' => self::ZONE_TYPES,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE FORM
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        if (!$this->adminId()) {
            return redirect()->route('admin.login');
        }

        $touristSpots = DB::table('tourist_spots')
            ->select('tourist_id', 'name')
            ->orderBy('name')
            ->get();

        return view('admin.geofence-zones.form', [
            'mode' => 'create',
            'zone' => null,
            'touristSpots' => $touristSpots,
            'zoneTypes' => self::ZONE_TYPES,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE NEW ZONE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $adminId = $this->requireAdmin();

        $data = $this->validatedZone($request);

        /*
        |--------------------------------------------------------------------------
        | POSTGIS GEOGRAPHY POINT
        |--------------------------------------------------------------------------
        |
        | ST_MakePoint uses LONGITUDE first, LATITUDE second.
        | SRID 4326 is the existing CityPulse coordinate system.
        |
        | Parameter binding prevents SQL injection.
        |
        */

        try {
            DB::insert(
                "
                INSERT INTO public.geofence_zones (
                    tourist_id,
                    zone_name,
                    zone_type,
                    center,
                    radius_meters,
                    alert_message,
                    created_by
                )
                VALUES (
                    ?,
                    ?,
                    ?,
                    extensions.ST_SetSRID(
                        extensions.ST_MakePoint(?, ?),
                        4326
                    )::extensions.geography,
                    ?,
                    ?,
                    ?
                )
                ",
                [
                    $data['tourist_id'] ?? null,
                    trim($data['zone_name']),
                    $data['zone_type'],
                    (float) $data['longitude'],
                    (float) $data['latitude'],
                    (int) $data['radius_meters'],
                    trim($data['alert_message']),
                    $adminId,
                ]
            );

            return redirect()
                ->route('admin.geofence-zones.index')
                ->with(
                    'success',
                    'Geofence zone created successfully.'
                );

        } catch (\Throwable $e) {
            Log::error(
                'CityPulse geofence creation failed',
                ['exception' => $e]
            );

            return back()
                ->withInput()
                ->withErrors([
                    'zone_name' =>
                        'Unable to create this zone. Please check the entered data and try again.',
                ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FIND SUPPORTED ZONE
    |--------------------------------------------------------------------------
    |
    | Emergency zones cannot be edited here.
    |
    */

    private function findZone(int $id): object
    {
        $zone = DB::table('geofence_zones')
            ->where('zone_id', $id)
            ->whereIn('zone_type', self::ZONE_TYPES)
            ->select(
                'zone_id',
                'tourist_id',
                'zone_name',
                'zone_type',
                'radius_meters',
                'alert_message'
            )
            ->selectRaw(
                'extensions.ST_Y(center::extensions.geometry) AS latitude'
            )
            ->selectRaw(
                'extensions.ST_X(center::extensions.geometry) AS longitude'
            )
            ->first();

        if (!$zone) {
            abort(404, 'Geofence zone not found.');
        }

        return $zone;
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT FORM
    |--------------------------------------------------------------------------
    */

    public function edit(int $id)
    {
        if (!$this->adminId()) {
            return redirect()->route('admin.login');
        }

        $zone = $this->findZone($id);

        $touristSpots = DB::table('tourist_spots')
            ->select('tourist_id', 'name')
            ->orderBy('name')
            ->get();

        return view('admin.geofence-zones.form', [
            'mode' => 'edit',
            'zone' => $zone,
            'touristSpots' => $touristSpots,
            'zoneTypes' => self::ZONE_TYPES,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE EXISTING ZONE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, int $id)
    {
        $this->requireAdmin();

        $data = $this->validatedZone($request);

        // Verify zone exists and is not an Emergency zone.
        $this->findZone($id);

        try {
            $affected = DB::update(
                "
                UPDATE public.geofence_zones
                SET
                    tourist_id = ?,
                    zone_name = ?,
                    zone_type = ?,
                    center = extensions.ST_SetSRID(
                        extensions.ST_MakePoint(?, ?),
                        4326
                    )::extensions.geography,
                    radius_meters = ?,
                    alert_message = ?
                WHERE zone_id = ?
                  AND zone_type IN (
                      'Safe',
                      'Medium Risk',
                      'High Risk',
                      'Tourist'
                  )
                ",
                [
                    $data['tourist_id'] ?? null,
                    trim($data['zone_name']),
                    $data['zone_type'],
                    (float) $data['longitude'],
                    (float) $data['latitude'],
                    (int) $data['radius_meters'],
                    trim($data['alert_message']),
                    $id,
                ]
            );

            if ($affected === 0) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'zone_name' =>
                            'The zone was not updated. It may no longer be available.',
                    ]);
            }

            return redirect()
                ->route('admin.geofence-zones.index')
                ->with(
                    'success',
                    'Geofence zone updated successfully.'
                );

        } catch (\Throwable $e) {
            Log::error(
                'CityPulse geofence update failed',
                ['exception' => $e]
            );

            return back()
                ->withInput()
                ->withErrors([
                    'zone_name' =>
                        'Unable to update this zone. Please try again.',
                ]);
        }
    }
}
