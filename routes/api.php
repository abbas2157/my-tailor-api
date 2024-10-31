<?php

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

Route::group(['prefix' => 'account'], function(){
    Route::post('create', [App\Http\Controllers\Api\AccountController::class, 'create_account']);
    Route::post('login', [App\Http\Controllers\Api\AccountController::class, 'login']);

    Route::post('send/code', [App\Http\Controllers\Api\AccountController::class, 'send_code']);
    Route::post('send/code/verify', [App\Http\Controllers\Api\AccountController::class, 'verify_code']);
    Route::post('send/code/reset/password', [App\Http\Controllers\Api\AccountController::class, 'reset_password']);

    Route::post('profile/upload', [App\Http\Controllers\Api\AccountController::class, 'profile_upload']);
    Route::post('profile/update', [App\Http\Controllers\Api\AccountController::class, 'profile_update']);

    Route::post('change/password', [App\Http\Controllers\Api\AccountController::class, 'change_password']);

    Route::post('create/shop', [App\Http\Controllers\Api\AccountController::class, 'createShop']);


});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
