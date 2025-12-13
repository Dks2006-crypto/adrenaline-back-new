<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;

class SiteSettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'site_name' => SiteSetting::get('site_name', 'Adrenaline Fitness'),
            'description' => SiteSetting::get('description', 'Ваш лучший фитнес-зал с незабываемыми ощущениями'),
            'email' => SiteSetting::get('email', 'info@adrenaline-fitness.ru'),
            'phone' => SiteSetting::get('phone', '+7 (903) 338-41-41'),
            'address' => SiteSetting::get('address', 'Двинская, 11, Волгоград'),
            'vk_url' => SiteSetting::get('vk_url'),
            'telegram_url' => SiteSetting::get('telegram_url'),
            'instagram_url' => SiteSetting::get('instagram_url'),
        ];

        return response()->json($settings);
    }
}
