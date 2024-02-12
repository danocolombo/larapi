<?php

use App\Http\Controllers\AffiliationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\DefaultGroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
/* call the index ProductController class for GET '/products' */

/**
 * unprotected (public) routes
 */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/organizations', [OrganizationController::class, 'index']);
Route::get('/organizations/{id}', [OrganizationController::class, 'show']);
Route::get('/organizations/search/{name}', [OrganizationController::class, 'search']);
Route::get('/meetings', [MeetingController::class, 'index']);
Route::get('/meetings/{id}', [MeetingController::class, 'show']);
Route::get('/meetings/search/{name}', [MeetingController::class, 'search']);
Route::get('/affiliations', [AffiliationController::class, 'index']);
Route::get('/affiliations/{id}', [AffiliationController::class, 'show']);
Route::get('/affiliations/target', [AffiliationController::class, 'target']);
Route::get('/defaultgroups', [DefaultGroupController::class, 'index']);
Route::get('/defaultgroups/{id}', [DefaultGroupController::class, 'show']);
Route::get('/defaultgroups/target', [DefaultGroupController::class, 'target']);
Route::get('/locations', [LocationController::class, 'index']);
Route::get('/locations/{id}', [LocationController::class, 'show']);
Route::get('/locations/search/{name}', [LocationController::class, 'search']);
Route::get('/people', [PersonController::class, 'index']);
Route::get('/people/{id}', [PersonController::class, 'show']);
Route::get('/people/search/{name}', [PersonController::class, 'search']);
Route::get('/system', [SystemSettingController::class, 'index']);
Route::get('/system/{id}', [SystemSettingController::class, 'show']);
// Route::get('/system/search/{name}', [SystemController::class, 'search']);
/**
 * protected Sanctum routes
 */
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/organizations', [OrganizationController::class, 'store']);
    Route::put('/organizations/{id}', [OrganizationController::class, 'update']);
    Route::delete('/organizations/{id}', [OrganizationController::class, 'destroy']);
    Route::post('/meetings', [MeetingController::class, 'store']);
    Route::put('/meetings/{id}', [MeetingController::class, 'update']);
    Route::delete('/meetings/{id}', [MeetingController::class, 'destroy']);
    Route::post('/affiliations', [AffiliationController::class, 'store']);
    Route::put('/affiliations/{id}', [AffiliationController::class, 'update']);
    Route::delete('/affiliations/{id}', [AffiliationController::class, 'destroy']);
    Route::post('/defaultgroups', [DefaultGroupController::class, 'store']);
    Route::put('/defaultgroups/{id}', [DefaultGroupController::class, 'update']);
    Route::delete('/defaultgroups/{id}', [DefaultGroupController::class, 'destroy']);
    Route::post('/locations', [LocationController::class, 'store']);
    Route::put('/locations/{id}', [LocationController::class, 'update']);
    Route::delete('/locations/{id}', [LocationController::class, 'destroy']);
    Route::post('/people', [PersonController::class, 'store']);
    Route::put('/people/{id}', [PersonController::class, 'update']);
    Route::delete('/people/{id}', [PersonController::class, 'destroy']);
    Route::post('/system', [SystemSettingController::class, 'store']);
    // Route::put('/system/{id}', [SystemController::class, 'update']);
    Route::delete('/system/{id}', [SystemSettingController::class, 'destroy']);
});

\Lomkit\Rest\Facades\Rest::resource('users', \App\Rest\Controllers\UsersController::class);
