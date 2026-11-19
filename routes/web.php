<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\CrimeRecordController;
use App\Http\Controllers\GeofenceZoneController;
use App\Http\Controllers\IncidentReportController;
use App\Http\Controllers\PredictiveAnalyticsController;
use App\Http\Controllers\RatingFeedbackController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SafetyAlertController;
use App\Http\Controllers\SafetyHeatMapController;
use App\Http\Controllers\TouristAccountController;
use App\Http\Controllers\TouristDensityMapController;
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
| Admin Notifications
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/notifications',
    [AdminNotificationController::class, 'index']
)->name('admin.notifications.index');

Route::post(
    '/admin/notifications/sync',
    [AdminNotificationController::class, 'sync']
)->name('admin.notifications.sync');

Route::post(
    '/admin/notifications/read-all',
    [AdminNotificationController::class, 'markAllRead']
)->name('admin.notifications.read-all');

Route::post(
    '/admin/notifications/{id}/read',
    [AdminNotificationController::class, 'markRead']
)->whereNumber('id')
  ->name('admin.notifications.read');


/*
|--------------------------------------------------------------------------
| Reports
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/reports',
    [ReportController::class, 'index']
)->name('admin.reports.index');

Route::get(
    '/admin/reports/export/pdf',
    [ReportController::class, 'exportPdf']
)->name('admin.reports.export.pdf');

Route::get(
    '/admin/reports/export/excel',
    [ReportController::class, 'exportExcel']
)->name('admin.reports.export.excel');


/*
|--------------------------------------------------------------------------
| Tourist Accounts
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/tourist-accounts',
    [TouristAccountController::class, 'index']
)->name('admin.tourist-accounts.index');

Route::patch(
    '/admin/tourist-accounts/{id}/status',
    [TouristAccountController::class, 'updateStatus']
)->name('admin.tourist-accounts.status');

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

Route::patch(
    '/admin/tourist-spots/{id}/status',
    [TouristSpotController::class, 'updateStatus']
)->name('admin.tourist-spots.status');

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

Route::delete(
    '/admin/incident-reports/{id}',
    [IncidentReportController::class, 'destroy']
)->name('admin.incident-reports.destroy');

Route::get(
    '/admin/incident-reports/{id}/image',
    [IncidentReportController::class, 'image']
)->name('admin.incident-reports.image');

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

Route::delete(
    '/admin/crime-records/{id}',
    [CrimeRecordController::class, 'destroy']
)->name('admin.crime-records.destroy');

Route::get(
    '/admin/crime-records/{id}',
    [CrimeRecordController::class, 'show']
)->name('admin.crime-records.show');


/*
|--------------------------------------------------------------------------
| Safety Alerts
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/safety-alerts',
    [SafetyAlertController::class, 'index']
)->name('admin.safety-alerts.index');

Route::get(
    '/admin/safety-alerts/create',
    [SafetyAlertController::class, 'create']
)->name('admin.safety-alerts.create');

Route::post(
    '/admin/safety-alerts',
    [SafetyAlertController::class, 'store']
)->name('admin.safety-alerts.store');

Route::get(
    '/admin/safety-alerts/{id}/edit',
    [SafetyAlertController::class, 'edit']
)->name('admin.safety-alerts.edit');

Route::put(
    '/admin/safety-alerts/{id}',
    [SafetyAlertController::class, 'update']
)->name('admin.safety-alerts.update');

Route::patch(
    '/admin/safety-alerts/{id}/status',
    [SafetyAlertController::class, 'updateStatus']
)->name('admin.safety-alerts.status');

Route::delete(
    '/admin/safety-alerts/{id}',
    [SafetyAlertController::class, 'destroy']
)->name('admin.safety-alerts.destroy');

Route::get(
    '/admin/safety-alerts/{id}',
    [SafetyAlertController::class, 'show']
)->name('admin.safety-alerts.show');


/*
|--------------------------------------------------------------------------
| Tourist Density Map
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/tourist-density-map',
    [TouristDensityMapController::class, 'index']
)->name('admin.tourist-density-map.index');


/*
|--------------------------------------------------------------------------
| Safety Heat Map
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/safety-heat-map',
    [SafetyHeatMapController::class, 'index']
)->name('admin.safety-heat-map.index');


/*
|--------------------------------------------------------------------------
| Geofence Zone Management
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/geofence-zones',
    [GeofenceZoneController::class, 'index']
)->name('admin.geofence-zones.index');

Route::get(
    '/admin/geofence-zones/create',
    [GeofenceZoneController::class, 'create']
)->name('admin.geofence-zones.create');

Route::post(
    '/admin/geofence-zones',
    [GeofenceZoneController::class, 'store']
)->name('admin.geofence-zones.store');

Route::get(
    '/admin/geofence-zones/{id}/edit',
    [GeofenceZoneController::class, 'edit']
)->whereNumber('id')
  ->name('admin.geofence-zones.edit');

Route::put(
    '/admin/geofence-zones/{id}',
    [GeofenceZoneController::class, 'update']
)->whereNumber('id')
  ->name('admin.geofence-zones.update');


/*
|--------------------------------------------------------------------------
| Predictive Analytics
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/predictive-analytics',
    [PredictiveAnalyticsController::class, 'index']
)->name('admin.predictive-analytics.index');


/*
|--------------------------------------------------------------------------
| Ratings & Feedback
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/ratings-feedback',
    [RatingFeedbackController::class, 'index']
)->name('admin.ratings-feedback.index');

Route::get(
    '/admin/ratings-feedback/{id}',
    [RatingFeedbackController::class, 'show']
)->name('admin.ratings-feedback.show');
