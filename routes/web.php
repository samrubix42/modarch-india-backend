<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::livewire('/login', 'auth::login')->name('login');
});

Route::post('/logout', function () {
    Auth::guard('web')->logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');


Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {
        Route::livewire('/dashboard', 'admin::dashboard')->name('dashboard');
        Route::livewire('/projects', 'admin::project.project-list')->name('projects');
        Route::livewire('/projects/{project}/sliders', 'admin::project.project-slider')->name('project-sliders');
        Route::livewire('/project-categories', 'admin::project-category-list')->name('project-categories');
        Route::livewire('/project-statuses', 'admin::status')->name('project-statuses');
        Route::livewire('/project-tags', 'admin::tag')->name('project-tags');
    });


