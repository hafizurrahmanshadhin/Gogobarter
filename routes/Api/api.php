<?php

use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\ExchangeRequestController;
use App\Http\Controllers\Api\HeaderAndFooterController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\StripeController;
use App\Http\Controllers\Api\SubscriptionPlanController;
use Illuminate\Support\Facades\Route;

// This route is for getting terms and conditions and privacy policy.
Route::get('contents', [ContentController::class, 'index'])->middleware(['throttle:10,1']);

Route::get('/subscription-plans/list', [SubscriptionPlanController::class, 'index']);
Route::get('/product-categories/list', [ProductCategoryController::class, 'index']);
Route::get('/header-footer', HeaderAndFooterController::class);
Route::get('/home', [HomeController::class, 'index'])->middleware('is_favorite');
Route::get('/home/search', [HomeController::class, 'search'])->middleware('is_favorite');
Route::get('/home/products/filter', [HomeController::class, 'filter'])->middleware('is_favorite');
Route::get('/home/product/details/{id}', [HomeController::class, 'show'])->middleware('is_favorite');
Route::get('/home/product/share/{id}', [HomeController::class, 'share']);
Route::post('/product/store', [ProductController::class, 'store'])->middleware('auth.jwt');
Route::post('/product/update/{id}', [ProductController::class, 'update'])->middleware('auth.jwt');
Route::delete('products/delete/{id}', [ProductController::class, 'destroy'])->middleware('auth.jwt');
Route::get('/dashboard/my-products', [ProductController::class, 'myProducts'])->middleware('auth.jwt');
Route::post('/product/toggle-favorite/{id}', [ProductController::class, 'toggleFavorite'])->middleware('auth.jwt');
Route::get('/dashboard/favorites/list', [ProductController::class, 'favoriteList'])->middleware('auth.jwt');

// User Profile and password Update
Route::get('/profile', [ProfileController::class, 'profile'])->middleware('auth.jwt');
Route::post('/dashboard/profile/update', [ProfileController::class, 'updateProfile'])->middleware('auth.jwt');
Route::post('/dashboard/password/update', [ProfileController::class, 'updatePassword'])->middleware('auth.jwt');

// Exchange request routes
Route::post('/exchange-request', [ExchangeRequestController::class, 'store'])->middleware('auth.jwt');
Route::get('/exchange-request/list', [ExchangeRequestController::class, 'myExchangeRequests'])->middleware('auth.jwt');
Route::post('/exchange-request/{id}/accept', [ExchangeRequestController::class, 'acceptRequest'])->middleware('auth.jwt');
Route::post('/exchange-request/{id}/decline', [ExchangeRequestController::class, 'declineRequest'])->middleware('auth.jwt');
Route::get('/exchange-request/{id}/offered-product', [ExchangeRequestController::class, 'offeredProductDetails'])->middleware('auth.jwt');

// Stripe payment routes
Route::post('/subscriptions/{plan}/checkout', [StripeController::class, 'checkout'])->middleware('auth.jwt');
Route::post('/stripe/webhook', [StripeController::class, 'webhook']);

// Notifications routes
Route::controller(NotificationController::class)->prefix('notifications')->middleware('auth.jwt')->group(function () {
    Route::get('/', 'index');
    Route::post('/read/{id}', 'markAsRead');
    Route::delete('/delete/{id}', 'destroy');
});
