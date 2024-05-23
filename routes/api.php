<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AffiliationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DefaultGroupController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PersonController;

/**
 * unprotected (public) routes
 */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/allusers', [AuthController::class, 'index']);
// Route::get('/meetings', [MeetingController::class, 'index']);
//* AFFILIATION
// Route::get('/affiliations', [AffiliationController::class, 'index']);
// Route::get('/affiliations/target', [AffiliationController::class, 'target']);
//* PERSON-PEOPLE routes


/**
 * protected Sanctum routes
 */
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    //* AFFILIATION protected routes
    Route::get('/affiliations', [AffiliationController::class, 'index']);
    Route::get('/affiliation/{id}', [AffiliationController::class, 'getAffiliation']);
    Route::post('/affiliation', [AffiliationController::class, 'store']);
    Route::put('/affiliation/{id}', [AffiliationController::class, 'update']);
    // Route::get('/affiliations/person/{id}', [AffiliationController::class, 'getAffiliationsForPerson']);
    Route::get('/affiliations/person/{person}', [AffiliationController::class, 'getAffiliationsForPerson'])->name('api.affiliations.person.paginated');
    Route::get('/affiliations/organization/{id}', [AffiliationController::class, 'getAffiliationForOrganization']);
    Route::delete('/affiliation/{id}', [AffiliationController::class, 'destroy']);
    //* DEFAULTGROUPS AND DEFAULTGROUP protected routes
    Route::get('/defaultgroups', [DefaultGroupController::class, 'index']);
    Route::get('/defaultgroups/{id}', [DefaultGroupController::class, 'list']);
    Route::get('/defaultgroup/{id}', [DefaultGroupController::class, 'find']);
    Route::get('/defaultgroups/target', [DefaultGroupController::class, 'target']);
    Route::post('/defaultgroup', [DefaultGroupController::class, 'store']);
    Route::put('/defaultgroup/{id}', [DefaultGroupController::class, 'update']);
    Route::delete('/defaultgroup/{id}', [DefaultGroupController::class, 'destroy']);
    //* GROUPS AND GROUP protected routes
    Route::get('/groups', [GroupController::class, 'index']);
    Route::get('/group/{id}', [GroupController::class, 'show']);
    Route::get('/groups/meeting/{id}', [GroupController::class, 'searchByMeetingId']);
    Route::post('/group', [GroupController::class, 'store']);
    Route::put('/group/{id}', [GroupController::class, 'update']);
    Route::delete('/group/{id}', [GroupController::class, 'destroy']);
    //* LOCATIONS AND LOCATION protected routes
    Route::get('/locations', [LocationController::class, 'index']);
    Route::get('/location/{id}', [LocationController::class, 'getLocationById']);
    Route::post('/location', [LocationController::class, 'store']);
    Route::put('/location/{id}', [LocationController::class, 'update']);
    Route::delete('/location/{id}', [LocationController::class, 'destroy']);
    //* MEETINGS AND MEETING protected routes
    Route::get('/meetings/{org}', [MeetingController::class, 'index']);
    Route::get('/meeting/{org}/{id}', [MeetingController::class, 'getOrgMeeting']);
    Route::post('/meeting', [MeetingController::class, 'store']);
    Route::put('/meeting/{org}/{id}', [MeetingController::class, 'update']);
    Route::delete('/meeting/{org}/{id}', [MeetingController::class, 'destroy']);
    //* ORGANIZATIONS AND ORGANIZATION protected routes
    Route::get('/organizations', [OrganizationController::class, 'index']);
    Route::get('/organization/{id}', [OrganizationController::class, 'getOrganizationById']);
    Route::get('/organizations/search', [OrganizationController::class, 'search']);
    Route::post('/organization', [OrganizationController::class, 'store']);
    Route::put('/organization/{id}', [OrganizationController::class, 'update']);
    Route::delete('/organization/{id}', [OrganizationController::class, 'destroy']);
    //* PERSON-PEOPLE protected routes
    Route::get('/people', [PersonController::class, 'index']);
    Route::get('/person/{id}', [PersonController::class, 'show']);
    Route::get('/people/search', [PersonController::class, 'search']);
    Route::get('/people/sub/{sub}', [PersonController::class, 'getPersonBySub']);
    Route::post('/person', [PersonController::class, 'store']);
    Route::put('/person/{id}', [PersonController::class, 'update']);
    Route::delete('/person/{id}', [PersonController::class, 'destroy']);
    Route::post('/person/{id}/image', [PersonController::class, 'uploadProfilePicture']);
    Route::get('/person/{id}/image-list', [PersonController::class, 'getProfilePictureList']);


    // Route::get('/people/sub/{id}', [PersonController::class, 'getSub']);
});
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');