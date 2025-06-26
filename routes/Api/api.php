<?php

use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\HeaderAndFooterController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SubscriptionPlanController;
use Illuminate\Support\Facades\Route;

// This route is for getting terms and conditions and privacy policy.
Route::get('contents', [ContentController::class, 'index'])->middleware(['throttle:10,1']);

Route::get('/subscription-plans/list', [SubscriptionPlanController::class, 'index']);
Route::get('/product-categories/list', [ProductCategoryController::class, 'index']);
Route::get('/header-footer', HeaderAndFooterController::class);
Route::get('/home', [HomeController::class, 'index']);
Route::get('/home/product/details/{id}', [HomeController::class, 'show']);
Route::post('/product/store', [ProductController::class, 'store'])->middleware('auth.jwt');
Route::post('/product/update/{id}', [ProductController::class, 'update'])->middleware('auth.jwt');
Route::get('/dashboard/my-products', [ProductController::class, 'myProducts'])->middleware('auth.jwt');
