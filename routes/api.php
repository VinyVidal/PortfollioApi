<?php

use App\Http\Controllers\ApiAuthController;
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

Route::post('auth', [ApiAuthController::class, 'auth']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('auth/revoke/{id?}', [ApiAuthController::class, 'revoke']);
    Route::delete('auth/revoke_all', [ApiAuthController::class, 'revokeAll']);

    Route::post('user', [UserController::class, 'store']);
});
