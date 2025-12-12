<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GroupClass;
use Illuminate\Http\Request;

class GroupClassController extends Controller
{
    // Получение списка групповых занятий для публичной страницы
    public function indexPublic()
    {
        $groupClasses = GroupClass::query()
            ->with(['trainer:id,name,last_name,avatar', 'service:id,title'])
            ->where('active', true)
            ->where('starts_at', '>', now())
            ->orderBy('starts_at')
            ->get()
            ->map(function ($groupClass) {
                $groupClass->available_slots = $groupClass->availableSlots();
                $groupClass->trainer_avatar_url = $groupClass->trainer?->avatar
                    ? url('storage/' . $groupClass->trainer->avatar) . '?t=' . time()
                    : null;

                return $groupClass;
            });

        return response()->json($groupClasses);
    }

    // Получение информации о конкретном групповом занятии
    public function show(GroupClass $groupClass)
    {
        $groupClass->load(['trainer:id,name,last_name,avatar', 'service:id,title', 'bookings']);
        $groupClass->available_slots = $groupClass->availableSlots();
        $groupClass->trainer_avatar_url = $groupClass->trainer?->avatar
            ? url('storage/' . $groupClass->trainer->avatar) . '?t=' . time()
            : null;

        return response()->json($groupClass);
    }

    // Создание нового группового занятия (для администраторов)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trainer_id' => 'nullable|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'starts_at' => 'required|date|after:now',
            'ends_at' => 'required|date|after:starts_at',
            'capacity' => 'required|integer|min:1',
            'price_cents' => 'required|integer|min:0',
            'currency' => 'required|string|size:3',
            'active' => 'boolean',
            'recurrence_rule' => 'nullable|string',
        ]);

        $groupClass = GroupClass::create($validated);

        return response()->json([
            'message' => 'Групповое занятие успешно создано',
            'group_class' => $groupClass
        ], 201);
    }

    // Обновление группового занятия (для администраторов)
    public function update(Request $request, GroupClass $groupClass)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'trainer_id' => 'nullable|exists:users,id',
            'service_id' => 'sometimes|exists:services,id',
            'starts_at' => 'sometimes|date|after:now',
            'ends_at' => 'sometimes|date|after:starts_at',
            'capacity' => 'sometimes|integer|min:1',
            'price_cents' => 'sometimes|integer|min:0',
            'currency' => 'sometimes|string|size:3',
            'active' => 'sometimes|boolean',
            'recurrence_rule' => 'nullable|string',
        ]);

        $groupClass->update($validated);

        return response()->json([
            'message' => 'Групповое занятие успешно обновлено',
            'group_class' => $groupClass
        ]);
    }

    // Удаление группового занятия (для администраторов)
    public function destroy(GroupClass $groupClass)
    {
        $groupClass->delete();

        return response()->json([
            'message' => 'Групповое занятие успешно удалено'
        ]);
    }
}
