<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Http\Controllers\TransientTokenController;
use Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController;
use Laravel\Passport\Http\Controllers\PersonalAccessTokenController;
use App\Http\Controllers\EmailController;

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

Route::post('/oauth/token', [AccessTokenController::class, 'issueToken']);
Route::post('/oauth/refresh', [TransientTokenController::class, 'refresh']);
Route::post('/oauth/revoke', [AuthorizedAccessTokenController::class, 'destroy']);

Route::middleware('auth:api')->group(function () {
    Route::get('/oauth/tokens', [AuthorizedAccessTokenController::class, 'forUser']);
    Route::delete('/oauth/tokens/{token_id}', [AuthorizedAccessTokenController::class, 'destroy']);
    Route::post('/oauth/personal-access-tokens', [PersonalAccessTokenController::class, 'store']);
    Route::get('/oauth/personal-access-tokens', [PersonalAccessTokenController::class, 'forUser']);
    Route::delete('/oauth/personal-access-tokens/{token_id}', [PersonalAccessTokenController::class, 'destroy']);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->get('/hello', function() {
    return response()->json([
        'message' => 'Hello World',
        'status' => 'success'
    ]);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/emails', [EmailController::class, 'store']);
    Route::get('/emails-stats', [EmailController::class, 'stats']);
});

