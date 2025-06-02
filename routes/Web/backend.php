<?php

use App\Http\Controllers\Web\Backend\CMS\HomePageHeroSectionController;
use App\Http\Controllers\Web\Backend\CMS\HomePageServiceSectionController;
use App\Http\Controllers\Web\Backend\DashboardController;
use Illuminate\Support\Facades\Route;

// Route for Admin Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// CMS
Route::prefix('cms')->group(function () {
    Route::prefix('home-page')->name('home-page.')->group(function () {
        Route::controller(HomePageHeroSectionController::class)->prefix('hero-section')->name('hero-section.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::patch('/', 'update')->name('update');
        });

        Route::controller(HomePageServiceSectionController::class)->prefix('service')->name('service.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::patch('/', 'update')->name('update');
        });
    });
});
