<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\AccommodationTypeController;
use App\Http\Controllers\Api\V1\InclusionController;
use App\Http\Controllers\Api\V1\RoomController;
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


Route::prefix('v1')->middleware(['web'])->group(function () {
    Route::get('/', function () {
        return 'Welcome to Chandava API';
    });
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::apiResource('users', UserController::class);
        Route::apiResource('accommodation-types', AccommodationTypeController::class);
        Route::apiResource('inclusions', InclusionController::class);
        Route::apiResource('rooms', RoomController::class);
    });
});
