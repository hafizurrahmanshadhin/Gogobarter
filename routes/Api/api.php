<?php

use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SubscriptionPlanController;
use Illuminate\Support\Facades\Route;

// This route is for getting terms and conditions and privacy policy.
Route::get('contents', [ContentController::class, 'index'])->middleware(['throttle:10,1']);

Route::get('/subscription-plans/list', [SubscriptionPlanController::class, 'index']);
Route::get('/product-categories/list', [ProductCategoryController::class, 'index']);
Route::get('/home', [HomeController::class, 'index']);
Route::post('/product/store', [ProductController::class, 'store'])->middleware('auth.jwt');
