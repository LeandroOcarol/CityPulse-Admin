<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (session()->has('admin_user')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $supabaseUrl = rtrim(config('services.supabase.url'), '/');
        $supabaseKey = config('services.supabase.key');

        if (!$supabaseUrl || !$supabaseKey) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Supabase configuration is missing.',
                ]);
        }

        try {
            $response = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Content-Type' => 'application/json',
            ])->post(
                $supabaseUrl . '/auth/v1/token?grant_type=password',
                [
                    'email' => $request->email,
                    'password' => $request->password,
                ]
            );

            if (!$response->successful()) {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors([
                        'email' => 'Invalid email or password.',
                    ]);
            }

            $authData = $response->json();

            $userId = $authData['user']['id'] ?? null;

            if (!$userId) {
                return back()->withErrors([
                    'email' => 'Unable to identify this account.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Find the matching CityPulse profile
            |--------------------------------------------------------------------------
            |
            | This supports either:
            | profiles.id = auth.users.id
            | OR
            | profiles.user_id = auth.users.id
            |
            */

            $profile = null;

            if (Schema::hasColumn('profiles', 'id')) {
                $profile = DB::table('profiles')
                    ->where('id', $userId)
                    ->first();
            }

            if (!$profile && Schema::hasColumn('profiles', 'user_id')) {
                $profile = DB::table('profiles')
                    ->where('user_id', $userId)
                    ->first();
            }

            if (!$profile) {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors([
                        'email' => 'No CityPulse profile was found for this account.',
                    ]);
            }

            if (!isset($profile->role) || strtolower($profile->role) !== 'admin') {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors([
                        'email' => 'Access denied. Administrator account required.',
                    ]);
            }

            $request->session()->regenerate();

            session([
                'admin_user' => [
                    'id' => $userId,
                    'email' => $authData['user']['email'] ?? $request->email,
                    'first_name' => $profile->first_name ?? '',
                    'last_name' => $profile->last_name ?? '',
                    'role' => $profile->role,
                ],
                'supabase_access_token' => $authData['access_token'] ?? null,
            ]);

            return redirect()->route('admin.dashboard');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Unable to connect to CityPulse. Please try again.',
                ]);
        }
    }

    public function dashboard()
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        return view('admin.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget([
            'admin_user',
            'supabase_access_token',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}