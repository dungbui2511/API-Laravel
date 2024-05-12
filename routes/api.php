<?php

use App\Http\Controllers\APIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::namespace('App\Http\Controllers')->group(function () {
    Route::get('users/{id?}', [APIController::class, 'getUsers']);
    Route::get('users-list',[APIController::class, 'getUsersList']);
    Route::post('add-users', [APIController::class, 'addUsers']);
    Route::post('add-multiple-users', [APIController::class, 'addMultipleUsers']);
    Route::put('update-users-details/{id}', [APIController::class, 'updateUserDetails']);
    // Register API-Register User with API token
    Route::post('register-user', [APIController::class,'registerUser']);
    // Register API-Register User witth passport
    Route::post('register-user-with-passport', [APIController::class,'registerUserWithPassport']);
    //Logout API -Logout user and delete  API token
    Route::post('logout-user',[APIController::class,'logoutUser']); 
    // Login API-Login user and update / return api token
    Route::post('login-user', [APIController::class,'loginUser']);
    // PATCH API - Update single records
    Route::patch('update-user-name/{id}',[APIController::class,'updateUserName']);
    // Post API - Login user with Passport
    Route::post('login-user-with-passport',[APIController::class,'loginUserWithPassport']);
    // Delete user
    Route::get('delete-user/{id}', [APIController::class, 'deleteUser']);
    // Delete API - multiple user
    Route::delete('delete-multiple-users/{ids}',[APIController::class,'deleteMultipleUsers']);
    // Delete API -Delete multiple users with json
    Route::delete('delete-multiple-users-with-json',[APIController::class, 'deleteMultipleUsersWithJson']);
});

