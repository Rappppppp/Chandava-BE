<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AssistantController;
use App\Http\Controllers\Api\V1\AccommodationTypeController;
use App\Http\Controllers\Api\V1\ContactUsFormController;
use App\Http\Controllers\Api\V1\ConversationController;
use App\Http\Controllers\Api\V1\ChangeScheduleRequestController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\InclusionController;
use App\Http\Controllers\Api\V1\RoomController;
use App\Http\Controllers\Api\V1\FilepondController;
use App\Http\Controllers\Api\V1\MyBookingController;
use App\Http\Controllers\Api\V1\FeedbackController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\AnalyticsController;
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


Route::prefix('v2')->group(function () {
    Route::get('/', function () {
        return 'Welcome to Chandava API';
    });

    Route::post('/assistant', [AssistantController::class, 'handle']);

    Route::controller(AuthController::class)
        ->group(function () {
            Route::post('/login', 'login');
            Route::post('/register', 'register');
            Route::post('/logout', 'logout');
        });

    Route::get('/analytics', [AnalyticsController::class, 'index']);

    Route::get('/public-rooms', [RoomController::class, 'publicIndex']);
    Route::get('/feedbacks', [FeedbackController::class, 'index']);

    Route::controller(ContactUsFormController::class)
        ->prefix('/contact-us')
        ->group(function () {
            Route::post('/', 'store');
            Route::get('/', 'index');
        });

    Route::prefix('/homepage')
        ->group(function () {
            Route::get('/feedbacks', [FeedbackController::class, 'top']);
        });

    Route::controller(ChangeScheduleRequestController::class)
        ->prefix('change-schedule')
        ->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/get/{user_id}', 'getByUserId');
            Route::post('/change-status', 'changeStatus');
        });

    Route::get('/analytics', [AnalyticsController::class, 'index']);

    // Authenticated routes
    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);

        Route::apiResource('bookings', MyBookingController::class);
        Route::controller(MyBookingController::class)
            ->group(function () {
                Route::get('/booking/{booking}', 'show');
                Route::patch('/update-booking-status', 'updateStatus');
                Route::patch('/update-booking-date', 'updateDate');
            });

        Route::controller(FeedbackController::class)
            ->group(function () {
                Route::post('/feedbacks', 'store');
                Route::post('/feedbacks-response', 'storeResponse');
            });

        Route::apiResource('users', UserController::class);
        Route::apiResource('accommodation-types', AccommodationTypeController::class);
        Route::apiResource('inclusions', InclusionController::class);

        // Room controller
        Route::apiResource('rooms', RoomController::class);
        Route::patch('/delete-room/{id}', [RoomController::class, 'deleteRoom']);
        Route::get('/room-list', [RoomController::class, 'roomList']);
        // Route::get('/contact-us', [ContactUsFormController::class, 'index']);

        Route::controller(FilepondController::class)
            ->group(function () {
                Route::post('filepond', 'store');
                Route::delete('filepond', 'revoke');
            });

        Route::controller(DashboardController::class)
            ->prefix('/dashboard')
            ->group(function () {
                Route::get('/', 'index');

                Route::prefix('/reports')
                    ->group(function () {
                        Route::get('/', 'getReports');
                        Route::get('/get-booking-years', 'getBookingYears');
                        Route::get('/get-report-details', 'getReportDetails');
                    });
            });

        Route::patch('/update-check-in-status', [RoomController::class, 'updateIsAlreadyCheckedIn']);

        Route::controller(ConversationController::class)
            ->prefix('conversations')
            ->group(function () {
                Route::post('/store', 'store');
                Route::get('/{user_id}', 'index');
                Route::get('/{conversation}/messages', 'messages');
                Route::post('/{conversation}/messages', 'sendMessage');
            });
    });
});
