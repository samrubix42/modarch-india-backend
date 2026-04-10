<?php

use App\Http\Controllers\Api\V1\JobApplicationController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\PublicDataController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/categories', [PublicDataController::class, 'categories'])->name('api.v1.categories.index');
    Route::get('/projects', [PublicDataController::class, 'projects'])->name('api.v1.projects.index');
    Route::get('/job-profiles', [PublicDataController::class, 'jobProfiles'])->name('api.v1.job-profiles.index');
    Route::get('/settings', [PublicDataController::class, 'settings'])->name('api.v1.settings.show');
    Route::post('/contacts', [ContactController::class, 'store'])
        ->middleware('throttle:contact-submissions')
        ->name('api.v1.contacts.store');
    Route::post('/job-applications', [JobApplicationController::class, 'store'])
        ->middleware('throttle:job-applications')
        ->name('api.v1.job-applications.store');
});
