<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'app_name',               'value' => 'Lottery Platform',   'group' => 'general',  'type' => 'string',  'label' => 'Application Name'],
            ['key' => 'default_locale',          'value' => 'en',                 'group' => 'general',  'type' => 'string',  'label' => 'Default Language'],
            ['key' => 'default_theme',           'value' => 'light',              'group' => 'general',  'type' => 'string',  'label' => 'Default Theme'],
            ['key' => 'max_screenshot_size_mb',  'value' => '5',                  'group' => 'upload',   'type' => 'integer', 'label' => 'Max Screenshot Size (MB)'],
            ['key' => 'allowed_payment_methods', 'value' => 'bank_transfer,mobile_money', 'group' => 'payment', 'type' => 'string', 'label' => 'Allowed Payment Methods'],
            ['key' => 'maintenance_mode',        'value' => '0',                  'group' => 'system',   'type' => 'boolean', 'label' => 'Maintenance Mode'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }

        $this->command->info('Default settings seeded.');
    }
}
