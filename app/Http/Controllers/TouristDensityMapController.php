<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TouristDensityMapController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TOURIST DENSITY CONFIGURATION
    |--------------------------------------------------------------------------
    |
    | GRID_SIZE:
    | 0.02 degrees of latitude is approximately 2.2 km.
    | Longitude distance varies depending on latitude.
    |
    | ACTIVITY_WINDOW_MINUTES:
    | Only tourist locations updated within the last
    | 15 minutes are included.
    |
    | MINIMUM_GROUP_SIZE:
    | An area must contain at least 5 distinct tourists
    | before its aggregated count can be displayed.
    |
    | These are provisional privacy safeguards.
    |
    */

    private const GRID_SIZE = 0.02;

    private const ACTIVITY_WINDOW_MINUTES = 15;

    private const MINIMUM_GROUP_SIZE = 5;

    public function index()
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
        | AGGREGATED TOURIST DENSITY
        |--------------------------------------------------------------------------
        |
        | Individual tourist identities and exact locations
        | must not be sent to the Admin Dashboard.
        |
        | The aggregation happens inside PostgreSQL.
        |
        | Only the center of each geographic grid cell
        | and its aggregate count are returned.
        |
        */

        $gridSize = self::GRID_SIZE;

        $cutoff = Carbon::now('UTC')
            ->subMinutes(self::ACTIVITY_WINDOW_MINUTES);

        /*
        |--------------------------------------------------------------------------
        | GRID COORDINATE EXPRESSIONS
        |--------------------------------------------------------------------------
        */

        $latitudeGridExpression = "
            FLOOR(
                extensions.ST_Y(
                    ul.location::extensions.geometry
                ) / {$gridSize}
            )
        ";

        $longitudeGridExpression = "
            FLOOR(
                extensions.ST_X(
                    ul.location::extensions.geometry
                ) / {$gridSize}
            )
        ";

        $latitudeCenterExpression = "
            (
                ({$latitudeGridExpression} + 0.5)
                * {$gridSize}
            )
        ";

        $longitudeCenterExpression = "
            (
                ({$longitudeGridExpression} + 0.5)
                * {$gridSize}
            )
        ";

        /*
        |--------------------------------------------------------------------------
        | QUERY AGGREGATED DENSITY
        |--------------------------------------------------------------------------
        */

        $densityAreas = DB::table('user_locations as ul')
            ->join(
                'profiles as p',
                'p.id',
                '=',
                'ul.user_id'
            )
            ->where('p.role', 'tourist')
            ->whereNotNull('ul.location')
            ->where(
                'ul.updated_at',
                '>=',
                $cutoff
            )
            ->selectRaw(
                "{$latitudeCenterExpression}
                AS latitude"
            )
            ->selectRaw(
                "{$longitudeCenterExpression}
                AS longitude"
            )
            ->selectRaw(
                'COUNT(DISTINCT ul.user_id)
                AS tourist_count'
            )
            ->groupByRaw(
                "{$latitudeGridExpression},
                {$longitudeGridExpression}"
            )
            ->havingRaw(
                'COUNT(DISTINCT ul.user_id) >= ?',
                [self::MINIMUM_GROUP_SIZE]
            )
            ->orderByDesc('tourist_count')
            ->get()
            ->map(function ($area) {

                return [
                    'latitude' => round(
                        (float) $area->latitude,
                        5
                    ),

                    'longitude' => round(
                        (float) $area->longitude,
                        5
                    ),

                    'tourist_count' => (int)
                        $area->tourist_count,
                ];

            })
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | DENSITY SUMMARY
        |--------------------------------------------------------------------------
        |
        | Summary values include only areas that meet
        | the minimum privacy threshold.
        |
        | Suppressed smaller groups are not counted.
        |
        */

        $visibleAreaCount = count($densityAreas);

        $visibleTouristCount = array_sum(
            array_column(
                $densityAreas,
                'tourist_count'
            )
        );

        $highestAreaCount = $visibleAreaCount > 0
            ? max(
                array_column(
                    $densityAreas,
                    'tourist_count'
                )
            )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | ACTIVE TOURIST SPOTS
        |--------------------------------------------------------------------------
        |
        | These are public destination coordinates,
        | not individual tourist coordinates.
        |
        */

        $touristSpots = DB::table('tourist_spots')
            ->where('is_active', true)
            ->whereNotNull('location')
            ->select(
                'tourist_id',
                'name',
                'category',
                'address',
                'opening_hours'
            )
            ->selectRaw(
                'extensions.ST_Y(
                    location::extensions.geometry
                ) AS latitude'
            )
            ->selectRaw(
                'extensions.ST_X(
                    location::extensions.geometry
                ) AS longitude'
            )
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | GEOFENCE ZONES
        |--------------------------------------------------------------------------
        |
        | Preserve existing geographic safety zones.
        | They may still be needed for the future
        | Geofence Zone Management module.
        |
        */

        $geofenceZones = DB::table('geofence_zones as gz')
            ->leftJoin(
                'tourist_spots as ts',
                'ts.tourist_id',
                '=',
                'gz.tourist_id'
            )
            ->whereNotNull('gz.center')
            ->select(
                'gz.zone_id',
                'gz.zone_name',
                'gz.zone_type',
                'gz.radius_meters',
                'gz.alert_message',
                'ts.name as tourist_spot_name'
            )
            ->selectRaw(
                'extensions.ST_Y(
                    gz.center::extensions.geometry
                ) AS latitude'
            )
            ->selectRaw(
                'extensions.ST_X(
                    gz.center::extensions.geometry
                ) AS longitude'
            )
            ->orderBy('gz.zone_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RETURN TOURIST DENSITY VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.tourist-density-map.index',
            [
                'densityAreas' => $densityAreas,

                'visibleAreaCount' => $visibleAreaCount,

                'visibleTouristCount' => $visibleTouristCount,

                'highestAreaCount' => $highestAreaCount,

                'minimumGroupSize' => self::MINIMUM_GROUP_SIZE,

                'activityWindowMinutes' =>
                    self::ACTIVITY_WINDOW_MINUTES,

                'touristSpots' => $touristSpots,

                'geofenceZones' => $geofenceZones,
            ]
        );
    }
}
