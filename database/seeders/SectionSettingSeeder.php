<?php

namespace Database\Seeders;

use App\Models\SectionSetting;
use Illuminate\Database\Seeder;

class SectionSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            [
                'section_key' => 'hero',
                'title' => 'Adrenaline Fitness - Твой путь к идеальной форме начинается здесь.',
                'subtitle' => 'Современный фитнес-клуб в центре Москвы',
                'description' => '• Просторные залы площадью 1500 кв.м с профессиональным оборудованием Technogym
• 50+ групповых программ: йога, пилатес, HIIT, кроссфит, танцы
• 20 персональных тренеров с международными сертификатами
• Удобное расположение: 5 минут от метро Тверская',
                'button_text' => 'Записаться на пробное занятие',
                'button_link' => '/book-trial',
                'image' => null, // Изображение будет загружено через админку
                'extra_data' => [
                    'background_overlay' => 'rgba(0,0,0,0.3)',
                    'text_color' => '#ffffff',
                    'cta_background' => '#ff6b35',
                    'cta_hover' => '#e05d2f',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'section_key' => 'about',
                'title' => 'О нас',
                'subtitle' => 'Фитнес-клуб нового поколения',
                'description' => 'Adrenaline Fitness - это не просто спортзал, а место, где вы можете полностью перезагрузиться. Мы создали все условия для эффективных тренировок и комфортного отдыха.

Наши преимущества:
• Работаем 24/7 для вашего удобства
• Wi-Fi, душевые кабины, сауна и хаммам
• Индивидуальный подход к каждому клиенту
• Современное оборудование и безопасная атмосфера',
                'button_text' => 'Подробнее о клубе',
                'button_link' => '/about',
                'image' => null,
                'extra_data' => [
                    'background_color' => '#f8f9fa',
                    'text_color' => '#333333',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'section_key' => 'services',
                'title' => 'Наши услуги',
                'subtitle' => 'Широкий выбор направлений для достижения ваших целей',
                'description' => '• Персональные тренировки с сертифицированными тренерами
• Групповые занятия по 15 направлениям
• Функциональный тренинг и кроссфит
• Реабилитационные программы после травм
• Консультации диетолога и составление рациона',
                'button_text' => 'Все услуги',
                'button_link' => '/services',
                'image' => null,
                'extra_data' => [
                    'background_color' => '#ffffff',
                    'text_color' => '#333333',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'section_key' => 'prices',
                'title' => 'Цены',
                'subtitle' => 'Доступные абонементы для каждого',
                'description' => 'Стандарт - 4500₽/мес
Премиум - 12000₽/мес
Студенческий - 3200₽/мес
Разовое посещение - 600₽

Акции:
• Первое посещение бесплатно
• Скидка 15% при оплате за 6 месяцев
• Приведи друга - получи 1000₽',
                'button_text' => 'Выбрать абонемент',
                'button_link' => '/prices',
                'image' => null,
                'extra_data' => [
                    'background_color' => '#1f2937',
                    'text_color' => '#ffffff',
                ],
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'section_key' => 'contacts',
                'title' => 'Контакты',
                'subtitle' => 'Готовы начать тренироваться?',
                'description' => 'Адрес: Москва, Тверская улица, 15
Телефон: +7 (495) 123-45-67
Email: info@adrenaline-fitness.ru
График работы: Ежедневно 6:00 - 24:00

Запишитесь на бесплатную консультацию прямо сейчас!',
                'button_text' => 'Связаться с нами',
                'button_link' => '/contacts',
                'image' => null,
                'extra_data' => [
                    'background_color' => '#0ea5e9',
                    'text_color' => '#ffffff',
                ],
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($sections as $section) {
            SectionSetting::updateOrCreate(
                ['section_key' => $section['section_key']],
                $section
            );
        }
    }
}
