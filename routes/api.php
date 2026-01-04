<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfumeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminDashboardController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/perfumes', [PerfumeController::class, 'index']);
Route::get('/perfumes/{perfume}', [PerfumeController::class, 'show']);


Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);

    
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [PaymentController::class, 'show']);
    Route::post('/orders/{order}/payment', [PaymentController::class, 'uploadProof']);

    
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::post('/perfumes', [PerfumeController::class, 'store']);
        Route::put('/perfumes/{perfume}', [PerfumeController::class, 'update']);
        Route::patch('/perfumes/{perfume}', [PerfumeController::class, 'update']);
        Route::delete('/perfumes/{perfume}', [PerfumeController::class, 'destroy']);
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        
        Route::get('/perfumes/{perfume}/images', [ImageController::class, 'index']);
        Route::post('/perfumes/{perfume}/images', [ImageController::class, 'storeSingle']);
        Route::post('/perfumes/{perfume}/images/batch', [ImageController::class, 'store']);
        Route::put('/perfumes/{perfume}/images/{image}/primary', [ImageController::class, 'setPrimary']);
        Route::delete('/perfumes/{perfume}/images/{image}', [ImageController::class, 'destroy']);

        
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index']);
        Route::get('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show']);
        Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update']);
        Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy']);

        
        Route::get('/reports', [AdminDashboardController::class, 'reports']);
        Route::get('/orders', [AdminDashboardController::class, 'orders']);
        Route::put('/orders/{order}/verify', [AdminDashboardController::class, 'verifyPayment']);
        Route::put('/orders/{order}/reject', [AdminDashboardController::class, 'rejectPayment']);
    });

    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});