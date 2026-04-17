<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Admin\Email\PreviewController;
use App\Http\Controllers\Api\Admin\Email\SendController;
use App\Http\Controllers\Api\Admin\Email\SendTestController;
use App\Http\Controllers\Api\Admin\EmailController;
use App\Http\Controllers\Api\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\Auth\EnrollmentTokenController;
use App\Http\Controllers\Api\Auth\RegisteredPasskeyController;
use App\Http\Controllers\Api\Newsletter\SubscribeController;
use App\Http\Controllers\Api\Newsletter\UnsubscribeController;
use App\Http\Controllers\Api\Newsletter\VerifyController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json(['status' => 'ok']));

Route::prefix('newsletter')->group(function (): void {
    Route::post('/subscribe', SubscribeController::class)->middleware('throttle:newsletter');
    Route::get('/verify/{token}', VerifyController::class);
    Route::get('/unsubscribe/{token}', UnsubscribeController::class);
});

Route::prefix('auth')->group(function (): void {
    Route::get('session', [AuthenticatedSessionController::class, 'create']);
    Route::post('session', [AuthenticatedSessionController::class, 'store']);
    Route::delete('session', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth:sanctum');

    Route::post('enroll/{token}', EnrollmentTokenController::class);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('passkey', [RegisteredPasskeyController::class, 'create']);
        Route::post('passkey', [RegisteredPasskeyController::class, 'store']);
    });
});

Route::middleware('auth:sanctum')->get('user', [UserController::class, 'show']);

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function (): void {
    Route::post('emails/preview', PreviewController::class);
    Route::post('emails/{email}/send', SendController::class)->middleware('throttle:admin-send');
    Route::post('emails/{email}/send-test', SendTestController::class)->middleware('throttle:admin-test');
    Route::apiResource('emails', EmailController::class);
});
