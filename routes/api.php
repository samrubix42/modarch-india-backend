<?php

use App\Http\Controllers\Api\V1\PublicDataController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/categories', [PublicDataController::class, 'categories'])->name('api.v1.categories.index');
    Route::get('/settings', [PublicDataController::class, 'settings'])->name('api.v1.settings.show');
});
