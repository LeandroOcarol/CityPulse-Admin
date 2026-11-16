<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SafetyHeatMapController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GEOGRAPHIC CONFIGURATION
    |--------------------------------------------------------------------------
    |
    | These coordinates describe a provisional Cebu-area
    | rectangular study window.
    |
    | This is NOT an official Cebu City boundary polygon.
    | It can include neighboring locations and exclude
    | parts of the city's administrative territory.
    |
    | Replace this with a verified administrative boundary
    | before final geographic validation.
    |
    */

    private const MIN_LATITUDE = 10.20;
    private const MAX_LATITUDE = 10.55;

    private const MIN_LONGITUDE = 123.72;
    private const MAX_LONGITUDE = 123.99;

    private const GRID_SIZE = 0.01;

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
        | PERIOD FILTER
        |--------------------------------------------------------------------------
        */

        $period = $request->query('period', 'all');

        if (!in_array(
            $period,
            ['all', '30days', '90days', 'year'],
            true
        )) {
            $period = 'all';
        }

        $today = Carbon::today('Asia/Manila');

        $startDate = null;
        $endDate = $today->toDateString();

        $periodLabel = 'All Available Records';

        switch ($period) {
            case '30days':
                $startDate = $today
                    ->copy()
                    ->subDays(29)
                    ->toDateString();

                $periodLabel = 'Last 30 Days';
                break;

            case '90days':
                $startDate = $today
                    ->copy()
                    ->subDays(89)
                    ->toDateString();

                $periodLabel = 'Last 90 Days';
                break;

            case 'year':
                $startDate = $today
                    ->copy()
                    ->startOfYear()
                    ->toDateString();

                $periodLabel = 'Current Year';
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | BASE QUERY
        |--------------------------------------------------------------------------
        |
        | The crime_date column is a calendar date.
        | Do not convert it between UTC and Manila time.
        |
        */

        $createBaseQuery = function () use (
            $startDate,
            $endDate
        ) {
            $query = DB::table('crime_data as cd')
                ->whereNotNull('cd.location');

            if ($startDate !== null) {
                $query->where(
                    'cd.crime_date',
                    '>=',
                    $startDate
                );
            }

            if ($startDate !== null) {
                $query->where(
                    'cd.crime_date',
                    '<=',
                    $endDate
                );
            }

            return $query;
        };

        /*
        |--------------------------------------------------------------------------
        | PROVISIONAL GEOGRAPHIC FILTER
        |--------------------------------------------------------------------------
        |
        | Filter by the crime record's stored PostGIS point.
        | All summary and map statistics use this same filter.
        |
        */

        $applyCoverageFilter = function ($query) {
            return $query
                ->whereRaw(
                    'extensions.ST_Y(
                        cd.location::extensions.geometry
                    ) BETWEEN ? AND ?',
                    [
                        self::MIN_LATITUDE,
                        self::MAX_LATITUDE,
                    ]
                )
                ->whereRaw(
                    'extensions.ST_X(
                        cd.location::extensions.geometry
                    ) BETWEEN ? AND ?',
                    [
                        self::MIN_LONGITUDE,
                        self::MAX_LONGITUDE,
                    ]
                );
        };

        /*
        |--------------------------------------------------------------------------
        | GEOGRAPHIC DATA QUALITY COUNTS
        |--------------------------------------------------------------------------
        */

        $allMappedRecordCount = $createBaseQuery()
            ->count();

        $coveredQuery = $applyCoverageFilter(
            $createBaseQuery()
        );

        $includedRecordCount = (clone $coveredQuery)
            ->count();

        $excludedRecordCount = max(
            0,
            $allMappedRecordCount - $includedRecordCount
        );

        /*
        |--------------------------------------------------------------------------
        | AGGREGATED GEOGRAPHIC CRIME DATA
        |--------------------------------------------------------------------------
        */

        $grid = self::GRID_SIZE;

        $latitudeGrid = "
            FLOOR(
                extensions.ST_Y(
                    cd.location::extensions.geometry
                ) / {$grid}
            )
        ";

        $longitudeGrid = "
            FLOOR(
                extensions.ST_X(
                    cd.location::extensions.geometry
                ) / {$grid}
            )
        ";

        $latitudeCenter = "
            ({$latitudeGrid} + 0.5) * {$grid}
        ";

        $longitudeCenter = "
            ({$longitudeGrid} + 0.5) * {$grid}
        ";

        $hotspots = $coveredQuery
            ->selectRaw(
                "{$latitudeCenter} AS latitude"
            )
            ->selectRaw(
                "{$longitudeCenter} AS longitude"
            )
            ->selectRaw(
                'COUNT(*) AS record_count'
            )
            ->selectRaw(
                '
                COALESCE(
                    SUM(
                        GREATEST(
                            COALESCE(cd.crime_count, 1),
                            0
                        )
                    ),
                    0
                ) AS incident_count
                '
            )
            ->selectRaw(
                "
                COALESCE(
                    SUM(
                        CASE
                            WHEN LOWER(
                                COALESCE(
                                    cd.severity::text,
                                    ''
                                )
                            ) = 'high'
                            THEN GREATEST(
                                COALESCE(cd.crime_count, 1),
                                0
                            )
                            ELSE 0
                        END
                    ),
                    0
                ) AS high_severity_count
                "
            )
            ->groupByRaw(
                "{$latitudeGrid}, {$longitudeGrid}"
            )
            ->orderByDesc('incident_count')
            ->get()
            ->map(function ($row) {
                return [
                    'latitude' => round(
                        (float) $row->latitude,
                        5
                    ),

                    'longitude' => round(
                        (float) $row->longitude,
                        5
                    ),

                    'record_count' => (int)
                        $row->record_count,

                    'incident_count' => (int)
                        $row->incident_count,

                    'high_severity_count' => (int)
                        $row->high_severity_count,
                ];
            })
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | SUMMARY STATISTICS
        |--------------------------------------------------------------------------
        */

        $totalRecords = array_sum(
            array_column(
                $hotspots,
                'record_count'
            )
        );

        $totalIncidents = array_sum(
            array_column(
                $hotspots,
                'incident_count'
            )
        );

        $highSeverityCount = array_sum(
            array_column(
                $hotspots,
                'high_severity_count'
            )
        );

        $hotspotCount = count($hotspots);

        $maximumIncidentCount = $hotspotCount > 0
            ? max(
                array_column(
                    $hotspots,
                    'incident_count'
                )
            )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.safety-heat-map.index',
            [
                'period' => $period,

                'periodLabel' => $periodLabel,

                'hotspots' => $hotspots,

                'totalRecords' => $totalRecords,

                'totalIncidents' => $totalIncidents,

                'highSeverityCount' => $highSeverityCount,

                'hotspotCount' => $hotspotCount,

                'maximumIncidentCount' =>
                    $maximumIncidentCount,

                'excludedRecordCount' =>
                    $excludedRecordCount,

                'coverageLabel' =>
                    'Provisional Cebu-Area Coverage',

                'coverageBounds' => [
                    'north' => self::MAX_LATITUDE,
                    'south' => self::MIN_LATITUDE,
                    'east' => self::MAX_LONGITUDE,
                    'west' => self::MIN_LONGITUDE,
                ],
            ]
        );
    }
}