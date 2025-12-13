<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\TrainerController;
use App\Http\Controllers\Api\SectionSettingController;
use App\Http\Controllers\Api\GroupClassController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\SiteSettingsController;
use App\Models\Form;
use App\Models\Service;
use App\Models\Coupon;

// Публичные эндпоинты для Hero секции
Route::get('/hero', [SectionSettingController::class, 'index']);

Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/refresh', [AuthController::class, 'refresh'])->name('api.refresh');

Route::get('/public/trainers', [TrainerController::class, 'indexPublic'])->name('api.public.trainers');

Route::get('/services', fn() => Service::where('active', true)->get());
Route::post('/purchase', [PurchaseController::class, 'store']);


// Публичные эндпоинты для групповых занятий
Route::get('/group-classes', [GroupClassController::class, 'indexPublic']);
Route::get('/group-classes/{groupClass}', [GroupClassController::class, 'show']);

Route::get('/gallery', [GalleryController::class, 'index']);

Route::get('/site-settings', [SiteSettingsController::class, 'index']);

// Проверка промокода
Route::post('/coupons/check', function (\Illuminate\Http\Request $request) {
    $request->validate(['code' => 'required|string']);

    $coupon = Coupon::where('code', $request->code)->first();

    if (!$coupon) {
        return response()->json([
            'valid' => false,
            'message' => 'Промокод не найден'
        ], 404);
    }

    if (!$coupon->isValid()) {
        return response()->json([
            'valid' => false,
            'message' => 'Промокод недействителен или истёк'
        ], 400);
    }

    return response()->json([
        'valid' => true,
        'discount_percent' => $coupon->discount_percent,
        'message' => "Скидка {$coupon->discount_percent}% применена!"
    ]);
});
Route::apiResource('bookings', BookingController::class)->only(['index', 'store']);

Route::middleware('auth:jwt')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
    Route::put('/me', [AuthController::class, 'update']);
    Route::patch('/me', [AuthController::class, 'update']);
    Route::post('/me/avatar', [AuthController::class, 'updateAvatar']);

    Route::get('/trainer/bookings', [TrainerController::class, 'indexBookings']);
    Route::patch('/trainer/bookings/{booking}', [TrainerController::class, 'updateBookingStatus']);
    Route::patch('/trainer/bookings/{booking}/comment', [TrainerController::class, 'updateTrainerComment']);
    Route::get('/memberships', [MembershipController::class, 'index']);

    Route::get('/classes', fn() => Form::with('service')->get());

    // Административные эндпоинты для групповых занятий
    Route::apiResource('group-classes', GroupClassController::class)
        ->only(['store', 'update', 'destroy']);
});
