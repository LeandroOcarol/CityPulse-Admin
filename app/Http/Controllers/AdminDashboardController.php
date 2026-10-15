<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $totalTourists = DB::table('profiles')
            ->where('role', 'tourist')
            ->count();

        $totalSpots = DB::table('tourist_spots')->count();

        $totalIncidents = DB::table('incidents')->count();

        $totalCrimeRecords = DB::table('crime_data')->count();

        $totalSafetyAlerts = DB::table('safety_alerts')->count();

        $completedTrips = DB::table('trip_history')
            ->where('status', 'completed')
            ->count();

        $recentIncidents = DB::table('incidents')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentTourists = DB::table('profiles')
            ->where('role', 'tourist')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalTourists',
            'totalSpots',
            'totalIncidents',
            'totalCrimeRecords',
            'totalSafetyAlerts',
            'completedTrips',
            'recentIncidents',
            'recentTourists'
        ));
    }
}