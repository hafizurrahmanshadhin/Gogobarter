<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\SubscriptionPlanController;
use App\Http\Controllers\Web\Backend\CMS\HomePageHeroSectionController;
use App\Http\Controllers\Web\Backend\CMS\HomePageServiceSectionController;
use App\Http\Controllers\Web\Backend\CMS\HomePageTradingSectionController;
use App\Http\Controllers\Web\Backend\CMS\HomePageInstructionSectionController;

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

        Route::controller(HomePageInstructionSectionController::class)->prefix('instruction')->name('instruction.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::patch('/', 'update')->name('update');
            Route::get('/show/{id}', 'show')->name('show');
            Route::put('/update/{id}', 'updateInstruction')->name('update-instruction');
            Route::get('/status/{id}', 'status')->name('status');
        });

        Route::controller(HomePageTradingSectionController::class)->prefix('trading')->name('trading.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::patch('/', 'update')->name('update');
        });
    });
});

// Subscription Plan
Route::controller(SubscriptionPlanController::class)->prefix('subscription-plan')->name('subscription-plan.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::patch('/update/{id}', 'update')->name('edit');
    Route::patch('/{subscription_plan}/toggle-status', 'toggleStatus')->name('toggle-status');
    Route::patch('/{subscription_plan}/toggle-recommended', 'toggleRecommended')->name('toggle-recommended');
});
