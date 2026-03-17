<?php

namespace Database\Seeders;

use Modules\Setting\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General
            [
                'key' => 'site_name',
                'name' => 'Site Name',
                'value' => 'INI CMS',
                'type' => 'string',
                'group' => 'general',
                'description' => 'The name of your website.',
            ],
            [
                'key' => 'site_description',
                'name' => 'Site Description',
                'value' => 'A modern headless CMS.',
                'type' => 'string',
                'group' => 'general',
                'description' => 'A brief description of your website.',
            ],
            // Appearance
            [
                'key' => 'site_logo',
                'name' => 'Site Logo',
                'value' => null,
                'type' => 'image',
                'group' => 'appearance',
                'description' => 'The logo of your website.',
            ],
            [
                'key' => 'primary_color',
                'name' => 'Primary Color',
                'value' => '#007bff',
                'type' => 'string',
                'group' => 'appearance',
                'description' => 'The primary color of your theme.',
            ],
            // Contact
            [
                'key' => 'contact_email',
                'name' => 'Contact Email',
                'value' => 'admin@example.com',
                'type' => 'string',
                'group' => 'contact',
                'description' => 'The email address for contact inquiries.',
            ],
            // Maintenance
            [
                'key' => 'maintenance_mode',
                'name' => 'Maintenance Mode',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'maintenance',
                'description' => 'Enable or disable maintenance mode.',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
