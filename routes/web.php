<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CrimeRecordController;
use App\Http\Controllers\IncidentReportController;
use App\Http\Controllers\TouristAccountController;
use App\Http\Controllers\TouristSpotController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Root
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('admin.login');
});


/*
|--------------------------------------------------------------------------
| Admin Authentication
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
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/dashboard',
    [AdminDashboardController::class, 'index']
)->name('admin.dashboard');


/*
|--------------------------------------------------------------------------
| Tourist Accounts
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
| Tourist Spots
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

Route::get(
    '/admin/tourist-spots/{id}/edit',
    [TouristSpotController::class, 'edit']
)->name('admin.tourist-spots.edit');

Route::put(
    '/admin/tourist-spots/{id}',
    [TouristSpotController::class, 'update']
)->name('admin.tourist-spots.update');

Route::get(
    '/admin/tourist-spots/{id}',
    [TouristSpotController::class, 'show']
)->name('admin.tourist-spots.show');


/*
|--------------------------------------------------------------------------
| Incident Reports
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/incident-reports',
    [IncidentReportController::class, 'index']
)->name('admin.incident-reports.index');

Route::patch(
    '/admin/incident-reports/{id}/status',
    [IncidentReportController::class, 'updateStatus']
)->name('admin.incident-reports.status');

Route::get(
    '/admin/incident-reports/{id}',
    [IncidentReportController::class, 'show']
)->name('admin.incident-reports.show');


/*
|--------------------------------------------------------------------------
| Crime Records
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/crime-records',
    [CrimeRecordController::class, 'index']
)->name('admin.crime-records.index');

Route::get(
    '/admin/crime-records/create',
    [CrimeRecordController::class, 'create']
)->name('admin.crime-records.create');

Route::post(
    '/admin/crime-records',
    [CrimeRecordController::class, 'store']
)->name('admin.crime-records.store');

Route::get(
    '/admin/crime-records/{id}/edit',
    [CrimeRecordController::class, 'edit']
)->name('admin.crime-records.edit');

Route::put(
    '/admin/crime-records/{id}',
    [CrimeRecordController::class, 'update']
)->name('admin.crime-records.update');

Route::get(
    '/admin/crime-records/{id}',
    [CrimeRecordController::class, 'show']
)->name('admin.crime-records.show');