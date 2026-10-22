<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $selectedCategory = $request->input(
            'category',
            'incident_reports'
        );

        $period = $request->input(
            'period',
            '30_days'
        );

        $startDate = $request->input(
            'start_date'
        );

        $endDate = $request->input(
            'end_date'
        );

        $visualization = $request->boolean(
            'visualization',
            true
        );


        $totalTourists = DB::table('profiles')
            ->where('role', 'tourist')
            ->count();

        $activeTourists = DB::table('profiles')
            ->where('role', 'tourist')
            ->where('is_active', true)
            ->count();

        $totalTouristSpots = DB::table('tourist_spots')
            ->count();

        $activeTouristSpots = DB::table('tourist_spots')
            ->where('is_active', true)
            ->count();

        $totalIncidents = DB::table('incidents')
            ->count();

        $pendingIncidents = DB::table('incidents')
            ->where('status', 'Pending')
            ->count();

        $approvedIncidents = DB::table('incidents')
            ->where('status', 'Approved')
            ->count();

        $rejectedIncidents = DB::table('incidents')
            ->where('status', 'Rejected')
            ->count();

        $totalCrimeRecords = DB::table('crime_data')
            ->count();

        $highSeverityCrimes = DB::table('crime_data')
            ->where('severity', 'High')
            ->count();

        $totalSafetyAlerts = DB::table('safety_alerts')
            ->count();

        $activeSafetyAlerts = DB::table('safety_alerts')
            ->where('is_active', true)
            ->count();

        $totalTrips = DB::table('trip_history')
            ->count();

        $completedTrips = DB::table('trip_history')
            ->where('status', 'Completed')
            ->count();


        $reportCategories = [

            'tourist_accounts' => [
                'title' => 'Tourist Accounts',
                'description' =>
                    'User growth, demographics, and account status.',
            ],

            'tourist_spots' => [
                'title' => 'Tourist Spots',
                'description' =>
                    'Tourist destinations and active location records.',
            ],

            'incident_reports' => [
                'title' => 'Incident Reports',
                'description' =>
                    'Incident submissions and review status.',
            ],

            'crime_records' => [
                'title' => 'Crime Records',
                'description' =>
                    'Historical crime data and severity information.',
            ],

            'safety_alerts' => [
                'title' => 'Safety Alerts',
                'description' =>
                    'Active and historical safety alert records.',
            ],

            'trip_history' => [
                'title' => 'Trip History',
                'description' =>
                    'Tourist journeys and completed trip activity.',
            ],

        ];


        $summary = $this->buildSummary(
            $selectedCategory,
            [
                'totalTourists' => $totalTourists,
                'activeTourists' => $activeTourists,

                'totalTouristSpots' => $totalTouristSpots,
                'activeTouristSpots' => $activeTouristSpots,

                'totalIncidents' => $totalIncidents,
                'pendingIncidents' => $pendingIncidents,
                'approvedIncidents' => $approvedIncidents,
                'rejectedIncidents' => $rejectedIncidents,

                'totalCrimeRecords' => $totalCrimeRecords,
                'highSeverityCrimes' => $highSeverityCrimes,

                'totalSafetyAlerts' => $totalSafetyAlerts,
                'activeSafetyAlerts' => $activeSafetyAlerts,

                'totalTrips' => $totalTrips,
                'completedTrips' => $completedTrips,
            ]
        );


        return view(
            'admin.reports.index',
            [
                'reportCategories' => $reportCategories,

                'selectedCategory' =>
                    $selectedCategory,

                'period' =>
                    $period,

                'startDate' =>
                    $startDate,

                'endDate' =>
                    $endDate,

                'visualization' =>
                    $visualization,

                'summary' =>
                    $summary,
            ]
        );
    }


    private function buildSummary(
        string $category,
        array $totals
    ): array {
        return match ($category) {

            'tourist_accounts' => [
                [
                    'label' =>
                        'Total Tourist Accounts',

                    'value' =>
                        $totals['totalTourists'],
                ],

                [
                    'label' =>
                        'Active Tourist Accounts',

                    'value' =>
                        $totals['activeTourists'],
                ],
            ],


            'tourist_spots' => [
                [
                    'label' =>
                        'Total Tourist Spots',

                    'value' =>
                        $totals['totalTouristSpots'],
                ],

                [
                    'label' =>
                        'Active Tourist Spots',

                    'value' =>
                        $totals['activeTouristSpots'],
                ],
            ],


            'crime_records' => [
                [
                    'label' =>
                        'Total Crime Records',

                    'value' =>
                        $totals['totalCrimeRecords'],
                ],

                [
                    'label' =>
                        'High Severity Records',

                    'value' =>
                        $totals['highSeverityCrimes'],
                ],
            ],


            'safety_alerts' => [
                [
                    'label' =>
                        'Total Safety Alerts',

                    'value' =>
                        $totals['totalSafetyAlerts'],
                ],

                [
                    'label' =>
                        'Active Safety Alerts',

                    'value' =>
                        $totals['activeSafetyAlerts'],
                ],
            ],


            'trip_history' => [
                [
                    'label' =>
                        'Total Trips',

                    'value' =>
                        $totals['totalTrips'],
                ],

                [
                    'label' =>
                        'Completed Trips',

                    'value' =>
                        $totals['completedTrips'],
                ],
            ],


            default => [
                [
                    'label' =>
                        'Total Incident Reports',

                    'value' =>
                        $totals['totalIncidents'],
                ],

                [
                    'label' =>
                        'Pending',

                    'value' =>
                        $totals['pendingIncidents'],
                ],

                [
                    'label' =>
                        'Approved',

                    'value' =>
                        $totals['approvedIncidents'],
                ],

                [
                    'label' =>
                        'Rejected',

                    'value' =>
                        $totals['rejectedIncidents'],
                ],
            ],

        };
    }
}