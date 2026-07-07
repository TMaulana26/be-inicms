<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\Models\Setting;

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
                'value' => [
                    'en' => 'INI CMS',
                    'id' => 'INI CMS',
                ],
                'type' => 'string',
                'group' => 'general',
                'description' => [
                    'en' => 'The name of your website.',
                    'id' => 'Nama situs web Anda.',
                ],
            ],
            [
                'key' => 'site_description',
                'name' => 'Site Description',
                'value' => [
                    'en' => 'A modern headless CMS.',
                    'id' => 'CMS headless modern.',
                ],
                'type' => 'string',
                'group' => 'general',
                'description' => [
                    'en' => 'A brief description of your website.',
                    'id' => 'Deskripsi singkat tentang situs web Anda.',
                ],
            ],
            // Appearance
            [
                'key' => 'site_logo',
                'name' => 'Site Logo',
                'value' => null,
                'type' => 'image',
                'group' => 'appearance',
                'description' => [
                    'en' => 'The logo of your website.',
                    'id' => 'Logo situs web Anda.',
                ],
            ],
            [
                'key' => 'primary_color',
                'name' => 'Primary Color',
                'value' => '#007bff',
                'type' => 'string',
                'group' => 'appearance',
                'description' => [
                    'en' => 'The primary color of your theme.',
                    'id' => 'Warna utama tema Anda.',
                ],
            ],
            // Contact
            [
                'key' => 'contact_email',
                'name' => 'Contact Email',
                'value' => 'admin@example.com',
                'type' => 'string',
                'group' => 'contact',
                'description' => [
                    'en' => 'The email address for contact inquiries.',
                    'id' => 'Alamat email untuk pertanyaan kontak.',
                ],
            ],
            // Maintenance
            [
                'key' => 'maintenance_mode',
                'name' => 'Maintenance Mode',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'maintenance',
                'description' => [
                    'en' => 'Enable or disable maintenance mode.',
                    'id' => 'Aktifkan atau nonaktifkan mode pemeliharaan.',
                ],
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
