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
                'class_id'   => 'nullable|integer|exists:group_classes,id',
                'note'       => 'nullable|string|max:1000',
            ]);

            $hasForm = $request->filled('form_id');
            $hasTrainer = $request->filled('trainer_id');
            $hasGroupClass = $request->filled('class_id');

            if (!$hasForm && !$hasTrainer && !$hasGroupClass) {
                return response()->json(['error' => 'Необходимо указать занятие или тренера'], 400);
            }

            // Запрещаем комбинации
            if (($hasForm + $hasTrainer + $hasGroupClass) > 1) {
                return response()->json(['error' => 'Нельзя указывать более одного типа занятия одновременно'], 400);
            }

            $user = auth('jwt')->user();
            if (!$user) {
                return response()->json(['error' => 'Не авторизован'], 401);
            }

            $isGroup = $request->filled('form_id') || $request->filled('class_id');

            $trainerId = null;
            $classId = null;

            if ($request->filled('form_id')) {
                $form = Form::findOrFail($request->form_id);
                if ($form->availableSlots() <= 0) {
                    return response()->json(['error' => 'Нет мест на занятии'], 400);
                }
                $trainerId = $form->trainer_id;
                $classId = $form->id;
            } elseif ($request->filled('class_id')) {
                $groupClass = \App\Models\GroupClass::findOrFail($request->class_id);
                if ($groupClass->availableSlots() <= 0) {
                    return response()->json(['error' => 'Нет мест на групповом занятии'], 400);
                }
                $trainerId = $groupClass->trainer_id;
                $classId = $groupClass->id;
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
                'user_id'        => $user->id,
                'class_id'       => $request->filled('form_id') ? $classId : null,
                'group_class_id' => $request->filled('class_id') ? $classId : null,
                'trainer_id'     => $trainerId,
                'status'         => $isGroup ? Booking::STATUS_CONFIRMED : Booking::STATUS_PENDING,
                'note'           => $request->note,
            ]);

            // Для групповых занятий не списываем посещения, так как это отдельная услуга
            // Списываем посещение только для персональных тренировок если не безлимит
            if (!$isGroup && $membership && $membership->remaining_visits !== null) {
                $membership->decrement('remaining_visits');
            }

            // Грузим связи безопасно
            if ($isGroup) {
                $booking->load(['groupClass.service', 'groupClass.trainer']);
            } else {
                $booking->load(['trainer']);
            }

            return response()->json([
                'message' => $isGroup
                    ? 'Вы успешно записаны на групповое занятие!'
                    : 'Запрос на персональную тренировку отправлен. Тренер свяжется с вами.',
                'booking' => $booking,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }



    public function index()
    {
        $bookings = Booking::query()
            ->where('user_id', auth('jwt')->user()->id)
            ->select('id', 'user_id', 'class_id', 'group_class_id', 'trainer_id', 'status', 'cancelled_at', 'note', 'trainer_comment', 'created_at')
            ->latest()
            ->with([
                // Для групповых занятий:
                'form.service',
                'form.trainer',
                'groupClass.service',
                'groupClass.trainer',
                // Для личных тренировок:
                'trainer',
            ])
            ->get()
            ->map(function ($booking) {
                // Определяем тип тренировки и формируем описание
                if ($booking->group_class_id) {
                    // Групповая тренировка
                    $booking->training_type = 'group';
                    $booking->training_description = 'Групповая тренировка';
                    if ($booking->groupClass) {
                        $booking->training_description .= ' - ' . ($booking->groupClass->service->title ?? 'Услуга');
                        if ($booking->groupClass->trainer) {
                            $booking->training_description .= ' с ' . $booking->groupClass->trainer->name;
                        }
                    }
                } elseif ($booking->class_id) {
                    // Форма тренировки (групповая)
                    $booking->training_type = 'form';
                    $booking->training_description = 'Групповая тренировка';
                    if ($booking->form && $booking->form->service) {
                        $booking->training_description .= ' - ' . $booking->form->service->title;
                    }
                    if ($booking->form && $booking->form->trainer) {
                        $booking->training_description .= ' с ' . $booking->form->trainer->name;
                    }
                } else {
                    // Персональная тренировка
                    $booking->training_type = 'personal';
                    $booking->training_description = 'Персональная тренировка';
                    if ($booking->trainer) {
                        $booking->training_description .= ' с ' . $booking->trainer->name;
                    }
                }

                return $booking;
            });

        return $bookings;
    }
}
