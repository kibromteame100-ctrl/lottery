<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\LotteryController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\TicketPurchaseController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ── Public Auth ───────────────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register'])
            ->middleware('throttle:10,1');
        Route::post('login', [AuthController::class, 'login'])
            ->middleware('throttle:10,1');
    });

    // ── Authenticated Mobile Endpoints ────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::patch('me/preferences', [AuthController::class, 'updatePreferences']);

        // Lotteries
        Route::get('lotteries', [LotteryController::class, 'index']);
        Route::get('lotteries/{lottery}', [LotteryController::class, 'show']);

        // Ticket purchases
        Route::get('ticket-purchases', [TicketPurchaseController::class, 'index']);
        Route::post('ticket-purchases', [TicketPurchaseController::class, 'store']);
        Route::get('ticket-purchases/{ticketPurchase}', [TicketPurchaseController::class, 'show']);

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::patch('notifications/{id}/read', [NotificationController::class, 'markRead']);
        Route::patch('notifications/read-all', [NotificationController::class, 'markAllRead']);
    });
});
