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
            'email' => 'admin@adrenaline-fitness.ru',
            'password' => bcrypt('admin123'),
            'phone' => '+7 (495) 123-45-67',
            'name' => 'Администратор',
            'role_id' => Role::where('name', 'admin')->first()->id,
            'confirmed_at' => now(),
        ]);

        // Тренер
        $trainerUser = User::create([
            'email' => 'alex@adrenaline-fitness.ru',
            'password' => bcrypt('trainer123'),
            'phone' => '+7 (495) 987-65-43',
            'name' => 'Алексей',
            'last_name' => 'Иванов',
            'role_id' => Role::where('name', 'trainer')->first()->id,
            'accepts_personal_bookings' => true,
        ]);

        // Клиент
        $clientUser = User::create([
            'email' => 'maria@adrenaline-fitness.ru',
            'password' => bcrypt('client123'),
            'name' => 'Мария',
            'last_name' => 'Сидорова',
            'phone' => '+7 (495) 555-12-34',
            'role_id' => Role::where('name', 'client')->first()->id,
        ]);

        // Тарифы (реальные цены для Москвы)

        // Абонемент "Стандарт" - 30 дней
        $standardService = Service::firstOrCreate(
            ['title' => 'Стандарт 30 дней'],
            [
                'description' => 'Базовый доступ к тренажерному залу и групповым занятиям',
                'base_benefits' => [
                    ['benefit' => 'Доступ к тренажерному залу 7 дней в неделю'],
                    ['benefit' => 'Посещение групповых занятий'],
                    ['benefit' => 'Раздевалки и душевые'],
                    ['benefit' => 'Wi-Fi и вода'],
                ],
                'duration_days' => 30,
                'visits_limit' => null,
                'price_cents' => 450000, // 4500 ₽
                'currency' => 'RUB',
                'active' => true,
                'type' => 'monthly',
            ]
        );

        // Абонемент "Премиум" - 30 дней
        $premiumService = Service::firstOrCreate(
            ['title' => 'Премиум 30 дней'],
            [
                'description' => 'Расширенный доступ + персональные тренировки',
                'base_benefits' => [
                    ['benefit' => 'Безлимитный доступ ко всем зонам'],
                    ['benefit' => '5 персональных тренировок в месяц'],
                    ['benefit' => 'Доступ к сауне и хаммаму'],
                    ['benefit' => 'Индивидуальный план тренировок'],
                    ['benefit' => 'Консультация диетолога'],
                ],
                'duration_days' => 30,
                'visits_limit' => null,
                'price_cents' => 1200000, // 12000 ₽
                'currency' => 'RUB',
                'active' => true,
                'type' => 'monthly',
            ]
        );

        // Абонемент "Студенческий" - 30 дней
        $studentService = Service::firstOrCreate(
            ['title' => 'Студенческий 30 дней'],
            [
                'description' => 'Специальные условия для студентов',
                'base_benefits' => [
                    ['benefit' => 'Доступ к тренажерному залу'],
                    ['benefit' => 'Посещение групповых занятий в будние дни'],
                    ['benefit' => 'Скидка 20% на персональные тренировки'],
                ],
                'duration_days' => 30,
                'visits_limit' => null,
                'price_cents' => 320000, // 3200 ₽
                'currency' => 'RUB',
                'active' => true,
                'type' => 'monthly',
            ]
        );

        // Пакет посещений - 8 посещений
        $visits8Service = Service::firstOrCreate(
            ['title' => '8 посещений'],
            [
                'description' => 'Гибкий пакет для нерегулярных посещений',
                'base_benefits' => [
                    ['benefit' => '8 посещений тренажерного зала'],
                    ['benefit' => 'Действует 60 дней'],
                    ['benefit' => 'Посещение групповых занятий'],
                ],
                'duration_days' => 60,
                'visits_limit' => 8,
                'price_cents' => 360000, // 3600 ₽
                'currency' => 'RUB',
                'active' => true,
                'type' => 'single',
            ]
        );

        // Пакет посещений - 16 посещений
        $visits16Service = Service::firstOrCreate(
            ['title' => '16 посещений'],
            [
                'description' => 'Экономия при покупке большего количества посещений',
                'base_benefits' => [
                    ['benefit' => '16 посещений тренажерного зала'],
                    ['benefit' => 'Действует 90 дней'],
                    ['benefit' => 'Посещение групповых занятий'],
                    ['benefit' => 'Скидка 10% по сравнению с 8 посещениями'],
                ],
                'duration_days' => 90,
                'visits_limit' => 16,
                'price_cents' => 680000, // 6800 ₽
                'currency' => 'RUB',
                'active' => true,
                'type' => 'single',
            ]
        );

        // Годовой абонемент
        Service::firstOrCreate(
            ['title' => 'Годовой безлимит'],
            [
                'description' => 'Максимальная выгода для постоянных клиентов',
                'base_benefits' => [
                    ['benefit' => 'Безлимит на весь год'],
                    ['benefit' => '30 персональных тренировок'],
                    ['benefit' => 'Доступ к VIP-зоне'],
                    ['benefit' => '2 гостевых визита в месяц'],
                    ['benefit' => 'Персональный менеджер'],
                    ['benefit' => 'Приоритетная запись на групповые занятия'],
                ],
                'duration_days' => 365,
                'visits_limit' => null,
                'price_cents' => 9900000, // 99000 ₽
                'currency' => 'RUB',
                'active' => true,
                'type' => 'yearly',
            ]
        );

        // Подписка для клиента (на стандартный тариф)
        Membership::create([
            'user_id' => $clientUser->id,
            'service_id' => $standardService->id,
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
            'status' => 'confirmed',
            'note' => 'Хочу похудеть и улучшить физическую форму',
            'trainer_comment' => 'Запись подтверждена на понедельник в 19:00. Приходите за 10 минут до начала.',
        ]);
    }
}
