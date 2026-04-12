<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Newsletter\SubscribeController;
use App\Http\Controllers\Api\Newsletter\UnsubscribeController;
use App\Http\Controllers\Api\Newsletter\VerifyController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json(['status' => 'ok']));

Route::prefix('newsletter')->group(function (): void {
    Route::post('/subscribe', SubscribeController::class)->middleware('throttle:newsletter');
    Route::get('/verify/{token}', VerifyController::class);
    Route::get('/unsubscribe/{token}', UnsubscribeController::class);
});
