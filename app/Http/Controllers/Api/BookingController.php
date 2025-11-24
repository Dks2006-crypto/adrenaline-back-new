<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Form;
use App\Models\User;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'form_id'    => 'nullable|integer|exists:forms,id',
                'trainer_id' => 'nullable|integer|exists:users,id',
                'note'       => 'nullable|string|max:1000',
            ]);

            if (!$request->filled('form_id') && !$request->filled('trainer_id')) {
                return response()->json(['error' => 'Необходимо указать занятие или тренера'], 400);
            }
            if ($request->filled('form_id') && $request->filled('trainer_id')) {
                return response()->json(['error' => 'Нельзя указывать и занятие, и тренера одновременно'], 400);
            }

            $user = auth('jwt')->user();
            if (!$user) {
                return response()->json(['error' => 'Не авторизован'], 401);
            }

            $isGroup = $request->filled('form_id');

            $trainerId = null;
            $classId = null;

            if ($isGroup) {
                $form = Form::findOrFail($request->form_id);
                if ($form->availableSlots() <= 0) {
                    return response()->json(['error' => 'Нет мест на занятии'], 400);
                }
                $trainerId = $form->trainer_id;
                $classId = $form->id;
            } else {
                $trainer = User::where('id', $request->trainer_id)
                    ->where('role_id', 2)
                    ->first();

                if (!$trainer) {
                    return response()->json(['error' => 'Тренер не найден'], 404);
                }
                $trainerId = $trainer->id;
            }

            // Проверка подписки
            $membership = $user->memberships()
                ->where('status', 'active')
                ->whereDate('end_date', '>=', now())
                ->first();

            if (!$membership) {
                return response()->json(['error' => 'У вас нет активной подписки'], 400);
            }

            if ($membership->remaining_visits !== null && $membership->remaining_visits <= 0) {
                return response()->json(['error' => 'Закончились посещения по подписке'], 400);
            }

            $booking = Booking::create([
                'user_id'    => $user->id,
                'class_id'   => $classId,
                'trainer_id' => $trainerId,
                'status'     => 'pending',
                'note'       => $request->note,
            ]);

            // Списываем посещение только если не безлимит
            if ($membership->remaining_visits !== null) {
                $membership->decrement('remaining_visits');
            }

            // Грузим связи безопасно
            $booking->load(['trainer', 'form.service']);

            return response()->json([
                'message' => $isGroup
                    ? 'Вы успешно записаны на занятие!'
                    : 'Запрос на персональную тренировку отправлен. Тренер свяжется с вами.',
                'booking' => $booking,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }



    public function index()
    {
        return Booking::query()
            ->where('user_id', auth('jwt')->user()->id)
            ->select('id', 'user_id', 'class_id', 'trainer_id', 'status', 'cancelled_at', 'note', 'trainer_comment', 'created_at')
            ->latest()
            ->with([
                // Для групповых занятий:
                'form.service',
                'form.trainer',
                // Для личных тренировок:
                'trainer',
            ])
            ->get();
    }
}
