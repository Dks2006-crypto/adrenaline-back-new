<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Role;
use App\Models\Service;
use App\Models\Trainer;
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
    // Роли
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'manager']);
    Role::create(['name' => 'trainer']);
    Role::create(['name' => 'client']);

    // Админ
    $admin = User::create([
        'email' => 'admin@fitness.ru',
        'password' => bcrypt('password'),
        'first_name' => 'Админ',
        'role_id' => Role::where('name', 'admin')->first()->id,
        'confirmed_at' => now(),
    ]);

    // Филиалы
    $branch = Branch::create([
        'name' => 'Фитнес-клуб "Сила"',
        'address' => 'ул. Ленина, 10',
        'timezone' => 'Europe/Moscow',
        'contact_phone' => '+7 (999) 123-45-67',
    ]);

    // Тренера
    $trainerUser = User::create([
        'email' => 'trainer@fitness.ru',
        'password' => bcrypt('password'),
        'first_name' => 'Алексей',
        'last_name' => 'Тренеров',
        'branch_id' => $branch->id,
        'role_id' => Role::where('name', 'trainer')->first()->id,
    ]);

    Trainer::create([
        'user_id' => $trainerUser->id,
        'bio' => 'Сертифицированный тренер по фитнесу и йоге.',
        'specialties' => ['yoga', 'pilates'],
    ]);

    // Тарифы
    Service::create([
        'title' => 'Месячный безлимит',
        'description' => 'Полный доступ ко всем залам',
        'duration_days' => 30,
        'visits_limit' => null,
        'price_cents' => 490000, // 4900.00 RUB
        'currency' => 'RUB',
        'branch_id' => $branch->id,
        'type' => 'monthly',
    ]);

    Service::create([
        'title' => '10 посещений',
        'description' => 'Разовые посещения',
        'duration_days' => 90,
        'visits_limit' => 10,
        'price_cents' => 350000,
        'currency' => 'RUB',
        'branch_id' => $branch->id,
        'type' => 'single',
    ]);
}
}
