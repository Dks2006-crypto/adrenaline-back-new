<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Membership;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SectionSettingSeeder::class,
            GroupClassSeeder::class,
        ]);

        // Админ
        $admin = User::create([
            'email' => 'admin@fitness.ru',
            'password' => bcrypt('password'),
            'phone' => '+7 (999) 123-45-67',
            'name' => 'Админ',
            'role_id' => Role::where('name', 'admin')->first()->id,
            'confirmed_at' => now(),
        ]);

        // Тренер
        $trainerUser = User::create([
            'email' => 'trainer@fitness.ru',
            'password' => bcrypt('password'),
            'phone' => '+7 (999) 123-45-67',
            'name' => 'Алексей',
            'last_name' => 'Тренеров',
            'role_id' => Role::where('name', 'trainer')->first()->id,
        ]);

        // Клиент
        $clientUser = User::create([
            'email' => 'client@fitness.ru',
            'password' => bcrypt('password'),
            'name' => 'Иван',
            'last_name' => 'Клиентов',
            'phone' => '+7 (999) 123-45-67',
            'role_id' => Role::where('name', 'client')->first()->id,
        ]);

        // Тарифы (с base_benefits и актуальными полями)

        $monthlyService = Service::firstOrCreate(
            ['title' => 'Месячный безлимит'],
            [
                'description' => 'Полный доступ ко всем залам и групповым занятиям',
                'base_benefits' => [
                    ['benefit' => 'Безлимитные посещения тренажёрного зала'],
                    ['benefit' => 'Доступ ко всем групповым занятиям'],
                    ['benefit' => 'Сауна и хаммам в подарок'],
                ],
                'duration_days' => 30,
                'visits_limit' => null,
                'price_cents' => 490000, // 4900 ₽
                'currency' => 'RUB',
                'active' => true,
                'type' => 'monthly',
            ]
        );

        $singleVisitsService = Service::firstOrCreate(
            ['title' => '10 посещений'],
            [
                'description' => 'Пакет разовых посещений на 90 дней',
                'base_benefits' => [
                    ['benefit' => '10 посещений тренажёрного зала'],
                    ['benefit' => 'Гибкий график — приходите когда удобно'],
                    ['benefit' => 'Действует 90 дней с момента активации'],
                ],
                'duration_days' => 90,
                'visits_limit' => 10,
                'price_cents' => 350000, // 3500 ₽
                'currency' => 'RUB',
                'active' => true,
                'type' => 'single',
            ]
        );

        // Ещё один пример — годовой тариф
        Service::firstOrCreate(
            ['title' => 'Годовой безлимит'],
            [
                'description' => 'Максимальная выгода для постоянных клиентов',
                'base_benefits' => [
                    ['benefit' => 'Безлимит на весь год'],
                    ['benefit' => '2 гостевых визита в месяц'],
                    ['benefit' => 'Персональная тренировка в подарок'],
                ],
                'duration_days' => 365,
                'visits_limit' => null,
                'price_cents' => 3990000, // 39 900 ₽
                'currency' => 'RUB',
                'active' => true,
                'type' => 'yearly',
            ]
        );

        // Подписка для клиента (на месячный безлимит)
        Membership::create([
            'user_id' => $clientUser->id,
            'service_id' => $monthlyService->id,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'remaining_visits' => null, // Безлимит
        ]);

        // Бронирование на персональную тренировку
        Booking::create([
            'user_id' => $clientUser->id,
            'trainer_id' => $trainerUser->id,
            'class_id' => null, // Персональная тренировка
            'status' => 'pending',
            'note' => 'Хочу похудеть и набрать мышечную массу',
            'trainer_comment' => 'Хорошо, давайте начнем с оценки вашего текущего состояния. Приходите в зал в понедельник в 10:00.',
        ]);
    }
}
