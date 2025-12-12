<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TrainerController extends Controller
{
    // Получение списка тренеров для публичной страницы
    public function indexPublic()
    {
        $trainers = User::where('role_id', 2)
            ->select('id', 'name', 'last_name', 'avatar', 'bio', 'specialties')
            ->get()
            ->map(function ($trainer) {
                $trainer->avatar_url = $trainer->avatar
                    ? url('storage/' . $trainer->avatar) . '?t=' . time()
                    : null;

                return $trainer;
            })
            ->makeHidden('avatar');

        return response()->json($trainers);
    }


    // Получение записей, привязанных к текущему авторизованному тренеру
    public function indexBookings()
    {
        /** @var \App\Models\User $trainer */
        $trainer = auth('jwt')->user();

        // Проверяем, что это тренер
        if (!$trainer || $trainer->role_id !== 2) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        // Получение только персональных тренировок (class_id = null)
        $bookings = Booking::query()
            ->where('trainer_id', $trainer->id)
            ->whereNull('class_id') // Только персональные тренировки
            ->select('id', 'user_id', 'class_id', 'trainer_id', 'status', 'cancelled_at', 'note', 'created_at')
            ->with([
                'user:id,name,phone,email' // Клиент, который записался
            ])
            ->orderBy('id', 'desc')
            ->get();

        // Добавить trainer_comment, если колонка существует
        if (Schema::hasColumn('bookings', 'trainer_comment')) {
            $bookings = $bookings->map(function ($booking) {
                $booking->trainer_comment = $booking->trainer_comment ?? null;
                return $booking;
            });
        }

        return response()->json($bookings);
    }

    public function updateBookingStatus(Request $request, Booking $booking)
    {
        /** @var \App\Models\User $trainer */
        $trainer = auth('jwt')->user();

        // 1. Проверка авторизации и роли
        if (!$trainer || $trainer->role_id !== 2) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        // 2. Проверка, что запись принадлежит этому тренеру
        // Это важно, чтобы тренер не мог менять чужие записи
        if ($booking->trainer_id !== $trainer->id) {
            return response()->json(['error' => 'Запись не принадлежит вам'], 403);
        }

        // 3. Валидация нового статуса
        $request->validate([
            'status' => 'required|in:' . \App\Models\Booking::STATUS_CONFIRMED . ',' . \App\Models\Booking::STATUS_CANCELLED,
        ]);

        // 4. Обновление статуса
        $newStatus = $request->status;
        $updateData = ['status' => $newStatus];

        if ($newStatus === 'cancelled') {
            $updateData['cancelled_at'] = now();
        }

        $booking->update($updateData);

        // 5. Ответ
        return response()->json([
            'message' => 'Статус записи изменен на ' . $newStatus,
            'booking' => $booking->load('user:id,name,phone,email'),
        ]);
    }

    public function updateTrainerComment(Request $request, Booking $booking)
    {
        /** @var \App\Models\User $trainer */
        $trainer = auth('jwt')->user();

        // 1. Проверка авторизации и роли
        if (!$trainer || $trainer->role_id !== 2) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        // 2. Проверка, что запись принадлежит этому тренеру
        if ($booking->trainer_id !== $trainer->id) {
            return response()->json(['error' => 'Запись не принадлежит вам'], 403);
        }

        // 3. Валидация комментария
        $request->validate([
            'trainer_comment' => 'nullable|string|max:1000',
        ]);

        // 4. Обновление комментария
        $booking->update([
            'trainer_comment' => $request->trainer_comment,
        ]);

        // 5. Ответ
        return response()->json([
            'message' => 'Комментарий обновлен',
            'booking' => $booking->load('user:id,name,phone,email'),
        ]);
    }
}
