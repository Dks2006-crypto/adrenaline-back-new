<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\TrainerController;
use App\Models\Form;
use App\Models\Service;

Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/refresh', [AuthController::class, 'refresh'])->name('api.refresh');

Route::get('/public/trainers', [TrainerController::class, 'indexPublic'])->name('api.public.trainers');

Route::get('/services', fn() => Service::where('active', true)->get());
Route::post('/purchase', [PurchaseController::class, 'store']);
Route::apiResource('bookings', BookingController::class)->only(['index', 'store']);

Route::middleware('auth:jwt')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
    Route::post('/me/avatar', [AuthController::class, 'updateAvatar']);

    Route::get('/trainer/bookings', [TrainerController::class, 'indexBookings']);
    Route::patch('/trainer/bookings/{booking}', [TrainerController::class, 'updateBookingStatus']);
    Route::patch('/trainer/bookings/{booking}/comment', [TrainerController::class, 'updateTrainerComment']);
    Route::get('/memberships', [MembershipController::class, 'index']);

    Route::get('/classes', fn() => Form::with('service')->get());
});
