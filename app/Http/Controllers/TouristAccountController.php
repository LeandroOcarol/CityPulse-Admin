<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TouristAccountController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Tourist Accounts Directory
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $search = trim($request->get('search', ''));
        $period = trim($request->get('period', 'all'));
        $status = trim($request->get('status', 'all'));

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
                'p.is_active',
                'p.created_at',
                'u.email'
            );

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
                ->orWhereRaw(
                    "CONCAT(p.first_name, ' ', p.last_name) ILIKE ?",
                    ['%' . $search . '%']
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

        if ($period === '7') {
            $query->where(
                'p.created_at',
                '>=',
                now()->subDays(7)
            );
        }

        if ($period === '30') {
            $query->where(
                'p.created_at',
                '>=',
                now()->subDays(30)
            );
        }

        if ($period === '90') {
            $query->where(
                'p.created_at',
                '>=',
                now()->subDays(90)
            );
        }

        if ($status === 'active') {
            $query->where('p.is_active', true);
        }

        if ($status === 'inactive') {
            $query->where('p.is_active', false);
        }

        $tourists = $query
            ->orderByDesc('p.is_active')
            ->orderByDesc('p.created_at')
            ->paginate(8)
            ->withQueryString();

        $totalTourists = DB::table('profiles')
            ->where('role', 'tourist')
            ->count();

        $activeTourists = DB::table('profiles')
            ->where('role', 'tourist')
            ->where('is_active', true)
            ->count();

        $inactiveTourists = DB::table('profiles')
            ->where('role', 'tourist')
            ->where('is_active', false)
            ->count();

        return view(
            'admin.tourist-accounts.index',
            [
                'tourists' => $tourists,
                'totalTourists' => $totalTourists,
                'activeTourists' => $activeTourists,
                'inactiveTourists' => $inactiveTourists,
                'search' => $search,
                'period' => $period,
                'status' => $status,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Tourist Account Details
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $tourist = $this->getTourist($id);

        abort_if(!$tourist, 404);

        $tripCount = DB::table('trip_history')
            ->where('user_id', $id)
            ->count();

        $completedTrips = DB::table('trip_history')
            ->where('user_id', $id)
            ->where('status', 'Completed')
            ->count();

        $reviewCount = DB::table('reviews')
            ->where('user_id', $id)
            ->count();

        $incidentCount = DB::table('incidents')
            ->where('user_id', $id)
            ->count();

        return view(
            'admin.tourist-accounts.show',
            [
                'tourist' => $tourist,
                'tripCount' => $tripCount,
                'completedTrips' => $completedTrips,
                'reviewCount' => $reviewCount,
                'incidentCount' => $incidentCount,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Activate / Deactivate Tourist Account
    |--------------------------------------------------------------------------
    */

    public function updateStatus(Request $request, $id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $tourist = DB::table('profiles')
            ->where('id', $id)
            ->where('role', 'tourist')
            ->first();

        abort_if(!$tourist, 404);

        $validated = $request->validate([
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        DB::table('profiles')
            ->where('id', $id)
            ->where('role', 'tourist')
            ->update([
                'is_active' => (bool) $validated['is_active'],
            ]);

        $message = $validated['is_active']
            ? 'Tourist account reactivated successfully.'
            : 'Tourist account deactivated successfully.';

        return back()->with(
            'success',
            $message
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Shared Tourist Query
    |--------------------------------------------------------------------------
    */

    private function getTourist($id)
    {
        return DB::table('profiles as p')
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
                'p.is_active',
                'p.created_at',
                'u.email',
                'u.last_sign_in_at'
            )
            ->first();
    }
}