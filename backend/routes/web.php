<?php

declare(strict_types=1);

use App\Http\Controllers\Dev\EmailPreviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

if (app()->environment('local')) {
    Route::prefix('dev/emails')->name('dev.email-previews.')->group(function (): void {
        Route::get('/', [EmailPreviewController::class, 'index'])->name('index');
        Route::get('/{template}', [EmailPreviewController::class, 'show'])->name('show');
    });
}
