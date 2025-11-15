<?php

use Illuminate\Support\Facades\Route;

// use App\Http\Controllers\Api\AuthController;
// use App\Http\Controllers\Api\BookingController;
// use App\Http\Controllers\Api\PurchaseController;
// use App\Models\Branch;
// use App\Models\Form;
// use App\Models\Service;
// use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });


// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

// // Защищённые
// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::get('/me', [AuthController::class, 'me']);

//     Route::post('/purchase', [PurchaseController::class, 'store']);
//     Route::apiResource('bookings', BookingController::class)->except(['update', 'destroy']);

//     Route::get('/services', fn() => Service::where('active', true)->get());
//     Route::get('/classes', fn() => Form::with('service')->get());
//     Route::get('/branches', fn() => Branch::all());
// });
