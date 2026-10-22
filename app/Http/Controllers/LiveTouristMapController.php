<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LiveTouristMapController extends Controller
{
    public function index()
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $tourists = DB::table('user_locations as ul')
            ->leftJoin(
                'profiles as p',
                'p.id',
                '=',
                'ul.user_id'
            )
            ->where('p.role', 'tourist')
            ->select(
                'ul.user_id',
                'ul.updated_at',
                'p.first_name',
                'p.last_name',
                'p.is_active',
                'p.profile_image_url'
            )
            ->selectRaw(
                'extensions.ST_Y(
                    ul.location::extensions.geometry
                ) AS latitude'
            )
            ->selectRaw(
                'extensions.ST_X(
                    ul.location::extensions.geometry
                ) AS longitude'
            )
            ->orderByDesc('ul.updated_at')
            ->get()
            ->map(function ($tourist) {

                $updatedAt = Carbon::parse(
                    $tourist->updated_at
                );

                $tourist->full_name = trim(
                    ($tourist->first_name ?? '') . ' ' .
                    ($tourist->last_name ?? '')
                );

                if ($tourist->full_name === '') {
                    $tourist->full_name = 'Unknown Tourist';
                }

                $tourist->location_status =
                    $updatedAt->greaterThanOrEqualTo(
                        now()->subMinutes(15)
                    )
                    ? 'Recent'
                    : 'Stale';

                $tourist->updated_at_manila =
                    $updatedAt
                        ->timezone('Asia/Manila')
                        ->format('M d, Y h:i A');

                return $tourist;
            });

        $totalTracked = $tourists->count();

        $recentCount = $tourists
            ->where('location_status', 'Recent')
            ->count();

        $staleCount = $tourists
            ->where('location_status', 'Stale')
            ->count();

        return view(
            'admin.live-tourist-map.index',
            [
                'tourists' => $tourists,
                'totalTracked' => $totalTracked,
                'recentCount' => $recentCount,
                'staleCount' => $staleCount,
            ]
        );
    }
}