<?php

namespace Database\Seeders;

use App\Models\GroupClass;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupClassSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем существующие услуги и тренеров
        $service = Service::firstOrCreate([
            'title' => 'Групповые тренировки',
        ], [
            'description' => 'Групповые занятия с тренером',
            'duration_days' => 30,
            'visits_limit' => 12,
            'price_cents' => 0,
            'currency' => 'RUB',
            'type' => 'group',
        ]);

        $trainer = User::where('role_id', 2)->first();

        // Создаем групповые занятия с будущими датами
        $startDate = now()->addDays(2); // Начинаем через 2 дня

        $groupClasses = [
            [
                'title' => 'Power Yoga',
                'description' => 'Интенсивная йога для развития силы, гибкости и выносливости',
                'service_id' => $service->id,
                'trainer_id' => $trainer?->id,
                'starts_at' => $startDate->copy()->setTime(8, 0),
                'ends_at' => $startDate->copy()->setTime(9, 0),
                'capacity' => 12,
                'currency' => 'RUB',
                'active' => true,
            ],
            [
                'title' => 'HIIT Интервал',
                'description' => 'Высокоинтенсивный интервальный тренинг для сжигания жира',
                'service_id' => $service->id,
                'trainer_id' => $trainer?->id,
                'starts_at' => $startDate->copy()->setTime(18, 0),
                'ends_at' => $startDate->copy()->setTime(19, 0),
                'capacity' => 15,
                'currency' => 'RUB',
                'active' => true,
            ],
            [
                'title' => 'Классический пилатес',
                'description' => 'Тренировка для укрепления мышц кора и улучшения осанки',
                'service_id' => $service->id,
                'trainer_id' => $trainer?->id,
                'starts_at' => $startDate->copy()->setTime(10, 0),
                'ends_at' => $startDate->copy()->setTime(11, 0),
                'capacity' => 10,
                'currency' => 'RUB',
                'active' => true,
            ],
            [
                'title' => 'Кардио-баттл',
                'description' => 'Энергичная тренировка с элементами бокса и фитнеса',
                'service_id' => $service->id,
                'trainer_id' => $trainer?->id,
                'starts_at' => $startDate->copy()->setTime(19, 30),
                'ends_at' => $startDate->copy()->setTime(20, 30),
                'capacity' => 14,
                'currency' => 'RUB',
                'active' => true,
            ],
            [
                'title' => 'Stretch & Relax',
                'description' => 'Растяжка и расслабление мышц после тренировок',
                'service_id' => $service->id,
                'trainer_id' => $trainer?->id,
                'starts_at' => $startDate->copy()->setTime(20, 0),
                'ends_at' => $startDate->copy()->setTime(21, 0),
                'capacity' => 10,
                'currency' => 'RUB',
                'active' => true,
            ],
            [
                'title' => 'Functional Training',
                'description' => 'Функциональный тренинг для развития всех физических качеств',
                'service_id' => $service->id,
                'trainer_id' => $trainer?->id,
                'starts_at' => $startDate->copy()->addDays(1)->setTime(7, 0),
                'ends_at' => $startDate->copy()->addDays(1)->setTime(8, 0),
                'capacity' => 16,
                'currency' => 'RUB',
                'active' => true,
            ],
            [
                'title' => 'TRX Suspension',
                'description' => 'Тренировка на висячих петлях для развития силы и баланса',
                'service_id' => $service->id,
                'trainer_id' => $trainer?->id,
                'starts_at' => $startDate->copy()->addDays(1)->setTime(17, 0),
                'ends_at' => $startDate->copy()->addDays(1)->setTime(18, 0),
                'capacity' => 8,
                'currency' => 'RUB',
                'active' => true,
            ],
            [
                'title' => 'Body Pump',
                'description' => 'Тренировка с гантелями для наращивания мышечной массы',
                'service_id' => $service->id,
                'trainer_id' => $trainer?->id,
                'starts_at' => $startDate->copy()->addDays(2)->setTime(9, 0),
                'ends_at' => $startDate->copy()->addDays(2)->setTime(10, 0),
                'capacity' => 18,
                'currency' => 'RUB',
                'active' => true,
            ],
            [
                'title' => 'Zumba Dance',
                'description' => 'Зажигательные танцевальные тренировки для похудения',
                'service_id' => $service->id,
                'trainer_id' => $trainer?->id,
                'starts_at' => $startDate->copy()->addDays(2)->setTime(20, 0),
                'ends_at' => $startDate->copy()->addDays(2)->setTime(21, 0),
                'capacity' => 20,
                'currency' => 'RUB',
                'active' => true,
            ],
            [
                'title' => 'Кроссфит',
                'description' => 'Интенсивная функциональная тренировка для продвинутых',
                'service_id' => $service->id,
                'trainer_id' => $trainer?->id,
                'starts_at' => $startDate->copy()->addDays(3)->setTime(18, 0),
                'ends_at' => $startDate->copy()->addDays(3)->setTime(19, 30),
                'capacity' => 12,
                'currency' => 'RUB',
                'active' => true,
            ],
        ];

        foreach ($groupClasses as $groupClass) {
            GroupClass::create($groupClass);
        }
    }
}
