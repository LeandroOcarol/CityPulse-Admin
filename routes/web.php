<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\TouristAccountController;
use App\Http\Controllers\TouristSpotController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('admin.login');
});


/*
|--------------------------------------------------------------------------
| ADMIN AUTHENTICATION
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/login',
    [AdminAuthController::class, 'showLogin']
)->name('admin.login');


Route::post(
    '/admin/login',
    [AdminAuthController::class, 'login']
)->name('admin.login.submit');


Route::post(
    '/admin/logout',
    [AdminAuthController::class, 'logout']
)->name('admin.logout');


/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/dashboard',
    [AdminDashboardController::class, 'index']
)->name('admin.dashboard');


/*
|--------------------------------------------------------------------------
| TOURIST ACCOUNTS
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/tourist-accounts',
    [TouristAccountController::class, 'index']
)->name('admin.tourist-accounts.index');


Route::get(
    '/admin/tourist-accounts/{id}',
    [TouristAccountController::class, 'show']
)->name('admin.tourist-accounts.show');


/*
|--------------------------------------------------------------------------
| TOURIST SPOTS
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/tourist-spots',
    [TouristSpotController::class, 'index']
)->name('admin.tourist-spots.index');


Route::get(
    '/admin/tourist-spots/create',
    [TouristSpotController::class, 'create']
)->name('admin.tourist-spots.create');


Route::post(
    '/admin/tourist-spots',
    [TouristSpotController::class, 'store']
)->name('admin.tourist-spots.store');