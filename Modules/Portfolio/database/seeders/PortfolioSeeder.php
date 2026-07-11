<?php

namespace Modules\Portfolio\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\Models\Project;

class PortfolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::create([
            'title' => [
                'en' => 'INI CMS - Headless Content Management System',
                'id' => 'INI CMS - Sistem Manajemen Konten Headless',
            ],
            'slug' => 'ini-cms-headless-content-management-system',
            'category' => 'BACKEND',
            'description' => [
                'en' => 'A robust headless CMS API built with Laravel 11 supporting authentication, RBAC, media library, and modular feature guides.',
                'id' => 'API CMS headless kokoh yang dibangun dengan Laravel 11 mendukung autentikasi, RBAC, media library, dan panduan fitur modular.',
            ],
            'tech_stack' => ['Laravel 11', 'Sanctum', 'Fortify', 'Spatie Permission', 'Spatie Media Library', 'Scramble'],
            'github_url' => 'https://github.com/TMaulana26/be-inicms',
            'demo_url' => 'https://api.inicms.com/docs/api',
            'is_active' => true,
        ]);

        Project::create([
            'title' => [
                'en' => 'Developer Portfolio Website',
                'id' => 'Situs Web Portofolio Developer',
            ],
            'slug' => 'developer-portfolio-website',
            'category' => 'FRONTEND',
            'description' => [
                'en' => 'A sleek, premium single page portfolio website built with Vue.js/Next.js consuming modular endpoints from the CMS.',
                'id' => 'Situs web portofolio satu halaman yang ramping dan premium dibangun dengan Vue.js/Next.js mengonsumsi endpoint modular dari CMS.',
            ],
            'tech_stack' => ['Vue.js', 'Next.js', 'Tailwind CSS', 'Vite'],
            'github_url' => 'https://github.com/TMaulana26/tm-portfolio',
            'demo_url' => 'https://portfolio.inicms.com',
            'is_active' => true,
        ]);
    }
}
