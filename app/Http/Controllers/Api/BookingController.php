<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Form; // ← ИСПРАВЛЕНО
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'form_id' => 'required|exists:forms,id',
        ]);

        $form = Form::findOrFail($request->form_id);
        $user = auth('jwt')->user();

        // Проверка мест
        if ($form->availableSlots() <= 0) {
            return response()->json(['error' => 'Нет мест'], 400);
        }

        // Проверка подписки
        $membership = $user->memberships()
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();

        if (!$membership || ($membership->remaining_visits !== null && $membership->remaining_visits <= 0)) {
            return response()->json(['error' => 'Нет активной подписки или посещений'], 400);
        }

        $booking = Booking::create([
            'user_id' => $user->id,
            'class_id' => $form->id,
            'status' => 'confirmed',
        ]);

        if ($membership->remaining_visits !== null) {
            $membership->decrement('remaining_visits');
        }

        return response()->json([
            'message' => 'Запись подтверждена',
            'booking' => $booking->load('form.service'),
        ]);
    }

    public function index()
    {
        return auth('sanctum')->user()
            ->bookings()
            ->with('form.service')
            ->get();
    }
}
