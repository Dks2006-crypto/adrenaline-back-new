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
            'name' => 'Админ',
            'role_id' => Role::where('name', 'admin')->first()->id,
            'confirmed_at' => now(),
        ]);


        // Тренера
        $trainerUser = User::create([
            'email' => 'trainer@fitness.ru',
            'password' => bcrypt('password'),
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

        // Подписка для клиента
        $monthlyService = Service::firstOrCreate([
            'title' => 'Месячный безлимит',
        ], [
            'description' => 'Полный доступ ко всем залам',
            'duration_days' => 30,
            'visits_limit' => null,
            'price_cents' => 490000,
            'currency' => 'RUB',
            'type' => 'monthly',
        ]);

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

        // Тарифы
        Service::create([
            'title' => 'Месячный безлимит',
            'description' => 'Полный доступ ко всем залам',
            'duration_days' => 30,
            'visits_limit' => null,
            'price_cents' => 490000, // 4900.00 RUB
            'currency' => 'RUB',
            'type' => 'monthly',
        ]);

        Service::create([
            'title' => '10 посещений',
            'description' => 'Разовые посещения',
            'duration_days' => 90,
            'visits_limit' => 10,
            'price_cents' => 350000,
            'currency' => 'RUB',
            'type' => 'single',
        ]);
    }
}
