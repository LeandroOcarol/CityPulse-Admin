<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeofenceZoneController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SUPPORTED ADMIN ZONE TYPES
    |--------------------------------------------------------------------------
    |
    | Emergency zones are excluded from this dashboard.
    |
    | Existing Emergency records are NOT deleted or modified.
    | Other applications that use those records are unaffected.
    |
    */

    private const ZONE_TYPES = [
        'Safe',
        'Medium Risk',
        'High Risk',
        'Tourist',
    ];

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | ADMIN AUTHENTICATION
        |--------------------------------------------------------------------------
        */

        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        /*
        |--------------------------------------------------------------------------
        | FILTERS
        |--------------------------------------------------------------------------
        */

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
        | SUPPORTED ZONE SUMMARY
        |--------------------------------------------------------------------------
        |
        | Count only the four supported zone classifications.
        |
        */

        $summaryRows = DB::table('geofence_zones')
            ->whereIn(
                'zone_type',
                self::ZONE_TYPES
            )
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

            if (
                array_key_exists(
                    $row->zone_type,
                    $counts
                )
            ) {

                $counts[$row->zone_type] = $count;

                $totalZones += $count;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | GEOFENCE DIRECTORY QUERY
        |--------------------------------------------------------------------------
        |
        | Read-only access to the shared Supabase database.
        |
        | No inserts, updates, or deletes are performed.
        |
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

        /*
        |--------------------------------------------------------------------------
        | ZONE TYPE FILTER
        |--------------------------------------------------------------------------
        */

        if ($selectedType !== 'all') {

            $query->where(
                'gz.zone_type',
                '=',
                $selectedType
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SEARCH FILTER
        |--------------------------------------------------------------------------
        */

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
        | LOAD ZONE DATA
        |--------------------------------------------------------------------------
        |
        | Coordinates are extracted from existing PostGIS
        | geometry values.
        |
        | No changes are made to stored geofence centers
        | or radius measurements.
        |
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
                '
                extensions.ST_Y(
                    gz.center::extensions.geometry
                ) AS latitude
                '
            )
            ->selectRaw(
                '
                extensions.ST_X(
                    gz.center::extensions.geometry
                ) AS longitude
                '
            )
            ->orderBy('gz.zone_name')
            ->get()
            ->map(function ($zone) {

                $latitude = $zone->latitude === null
                    ? null
                    : (float) $zone->latitude;

                $longitude = $zone->longitude === null
                    ? null
                    : (float) $zone->longitude;

                return [
                    'zone_id' => $zone->zone_id,

                    'zone_name' =>
                        $zone->zone_name,

                    'zone_type' =>
                        $zone->zone_type,

                    'radius_meters' => (float) (
                        $zone->radius_meters ?? 0
                    ),

                    'alert_message' =>
                        $zone->alert_message,

                    'tourist_spot_name' =>
                        $zone->tourist_spot_name,

                    'latitude' => $latitude,

                    'longitude' => $longitude,
                ];

            })
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | COUNT VALID MAPPED ZONES
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

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.geofence-zones.index',
            [
                'zones' => $zones,

                'counts' => $counts,

                'totalZones' => $totalZones,

                'filteredZoneCount' =>
                    count($zones),

                'mappedZoneCount' =>
                    $mappedZoneCount,

                'selectedType' =>
                    $selectedType,

                'search' => $search,

                'zoneTypes' =>
                    self::ZONE_TYPES,
            ]
        );
    }
}