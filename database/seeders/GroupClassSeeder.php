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
        $startDate = now()->addDays(7); // Начинаем через неделю

        $groupClasses = [
            [
                'title' => 'Йога для начинающих',
                'description' => 'Утренняя йога для улучшения гибкости и расслабления',
                'service_id' => $service->id,
                'trainer_id' => $trainer?->id,
                'starts_at' => $startDate->copy()->setTime(9, 0),
                'ends_at' => $startDate->copy()->setTime(10, 30),
                'capacity' => 15,
                'price_cents' => 80000,
                'currency' => 'RUB',
                'active' => true,
                'recurrence_rule' => 'FREQ=WEEKLY;BYDAY=MO,WE,FR',
            ],
            [
                'title' => 'Функциональный тренинг',
                'description' => 'Высокоинтенсивная тренировка для всего тела',
                'service_id' => $service->id,
                'trainer_id' => $trainer?->id,
                'starts_at' => $startDate->copy()->addDays(1)->setTime(18, 0),
                'ends_at' => $startDate->copy()->addDays(1)->setTime(19, 0),
                'capacity' => 20,
                'price_cents' => 100000,
                'currency' => 'RUB',
                'active' => true,
                'recurrence_rule' => 'FREQ=WEEKLY;BYDAY=TU,TH',
            ],
            [
                'title' => 'Пилатес',
                'description' => 'Тренировка для укрепления мышц кора и улучшения осанки',
                'service_id' => $service->id,
                'trainer_id' => $trainer?->id,
                'starts_at' => $startDate->copy()->addDays(2)->setTime(10, 0),
                'ends_at' => $startDate->copy()->addDays(2)->setTime(11, 0),
                'capacity' => 12,
                'price_cents' => 90000,
                'currency' => 'RUB',
                'active' => true,
                'recurrence_rule' => 'FREQ=WEEKLY;BYDAY=SA',
            ],
            [
                'title' => 'Кроссфит',
                'description' => 'Интенсивная тренировка с элементами тяжелой атлетики',
                'service_id' => $service->id,
                'trainer_id' => $trainer?->id,
                'starts_at' => $startDate->copy()->addDays(3)->setTime(19, 0),
                'ends_at' => $startDate->copy()->addDays(3)->setTime(20, 30),
                'capacity' => 10,
                'price_cents' => 120000,
                'currency' => 'RUB',
                'active' => true,
                'recurrence_rule' => 'FREQ=WEEKLY;BYDAY=SU',
            ],
        ];

        foreach ($groupClasses as $groupClass) {
            GroupClass::create($groupClass);
        }
    }
}
