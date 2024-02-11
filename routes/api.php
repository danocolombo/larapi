<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\SystemSettingController;
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
Route::get('/locations', [LocationController::class, 'index']);
Route::get('/locations/{id}', [LocationController::class, 'show']);
Route::get('/locations/search/{name}', [LocationController::class, 'search']);
Route::get('/people', [PersonController::class, 'index']);
Route::get('/people/{id}', [PersonController::class, 'show']);
Route::get('/people/search/{name}', [PersonController::class, 'search']);
Route::get('/app', [SystemSettingController::class, 'index']);
// Route::get('/system/{id}', [SystemController::class, 'show']);
// Route::get('/system/search/{name}', [SystemController::class, 'search']);
/**
 * protected Sanctum routes
 */
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/organizations', [OrganizationController::class, 'store']);
    Route::put('/organizations/{id}', [OrganizationController::class, 'update']);
    Route::delete('/organizations/{id}', [OrganizationController::class, 'destroy']);
    Route::post('/locations', [LocationController::class, 'store']);
    Route::put('/locations/{id}', [LocationController::class, 'update']);
    Route::delete('/locations/{id}', [LocationController::class, 'destroy']);
    Route::post('/people', [PersonController::class, 'store']);
    Route::put('/people/{id}', [PersonController::class, 'update']);
    Route::delete('/people/{id}', [PersonController::class, 'destroy']);
    // Route::post('/system', [SystemController::class, 'store']);
    // Route::put('/system/{id}', [SystemController::class, 'update']);
    // Route::delete('/system/{id}', [SystemController::class, 'destroy']);
});

\Lomkit\Rest\Facades\Rest::resource('users', \App\Rest\Controllers\UsersController::class);
