<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\BookingController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);

Route::middleware('auth:jwt')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/purchase', [PurchaseController::class, 'store']);
    Route::apiResource('bookings', BookingController::class)->only(['index', 'store']);

    Route::get('/services', fn() => \App\Models\Service::where('active', true)->get());
    Route::get('/classes', fn() => \App\Models\Form::with('service')->get());
    Route::get('/branches', fn() => \App\Models\Branch::all());
});
