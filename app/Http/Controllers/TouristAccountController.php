<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TouristAccountController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TOURIST ACCOUNT DIRECTORY
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $search = trim($request->get('search', ''));
        $period = $request->get('period', 'all');


        $query = DB::table('profiles as p')
            ->leftJoin(
                'auth.users as u',
                'u.id',
                '=',
                'p.id'
            )
            ->where('p.role', 'tourist')
            ->select(
                'p.id',
                'p.first_name',
                'p.last_name',
                'p.phone_number',
                'p.role',
                'p.profile_image_url',
                'p.created_at',
                'u.email'
            );


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $query->where(function ($q) use ($search) {

                $q->where(
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
                    'p.phone_number',
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


        /*
        |--------------------------------------------------------------------------
        | DATE FILTER
        |--------------------------------------------------------------------------
        */

        if ($period === '7') {

            $query->where(
                'p.created_at',
                '>=',
                now()->subDays(7)
            );

        } elseif ($period === '30') {

            $query->where(
                'p.created_at',
                '>=',
                now()->subDays(30)
            );

        } elseif ($period === '90') {

            $query->where(
                'p.created_at',
                '>=',
                now()->subDays(90)
            );

        }


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $tourists = $query
            ->orderByDesc('p.created_at')
            ->paginate(8)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | TOTAL TOURIST COUNT
        |--------------------------------------------------------------------------
        */

        $totalTourists = DB::table('profiles')
            ->where('role', 'tourist')
            ->count();


        return view(
            'admin.tourist-accounts.index',
            [
                'tourists' => $tourists,
                'totalTourists' => $totalTourists,
                'search' => $search,
                'period' => $period,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VIEW TOURIST ACCOUNT
    |--------------------------------------------------------------------------
    */

    public function show(string $id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }


        $tourist = DB::table('profiles as p')
            ->leftJoin(
                'auth.users as u',
                'u.id',
                '=',
                'p.id'
            )
            ->where('p.id', $id)
            ->where('p.role', 'tourist')
            ->select(
                'p.id',
                'p.first_name',
                'p.last_name',
                'p.phone_number',
                'p.role',
                'p.profile_image_url',
                'p.created_at',
                'u.email',
                'u.last_sign_in_at'
            )
            ->first();


        if (!$tourist) {
            abort(404);
        }


        $tripCount = DB::table('trip_history')
            ->where('user_id', $id)
            ->count();


        $completedTrips = DB::table('trip_history')
            ->where('user_id', $id)
            ->where('status', 'completed')
            ->count();


        $reviewCount = DB::table('reviews')
            ->where('user_id', $id)
            ->count();


        return view(
            'admin.tourist-accounts.show',
            [
                'tourist' => $tourist,
                'tripCount' => $tripCount,
                'completedTrips' => $completedTrips,
                'reviewCount' => $reviewCount,
            ]
        );
    }
}