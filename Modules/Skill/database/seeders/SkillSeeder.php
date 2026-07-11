<?php

namespace Modules\Skill\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Skill\Models\Skill;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            // Frontend
            ['name' => 'Vue.js', 'category' => 'frontend', 'order' => 1, 'is_active' => true],
            ['name' => 'Nuxt.js', 'category' => 'frontend', 'order' => 2, 'is_active' => true],
            ['name' => 'React', 'category' => 'frontend', 'order' => 3, 'is_active' => true],
            ['name' => 'Next.js', 'category' => 'frontend', 'order' => 4, 'is_active' => true],
            ['name' => 'Tailwind CSS', 'category' => 'frontend', 'order' => 5, 'is_active' => true],
            ['name' => 'HTML5 & CSS3', 'category' => 'frontend', 'order' => 6, 'is_active' => true],
            ['name' => 'JavaScript', 'category' => 'frontend', 'order' => 7, 'is_active' => true],
            ['name' => 'TypeScript', 'category' => 'frontend', 'order' => 8, 'is_active' => true],

            // Backend
            ['name' => 'Laravel', 'category' => 'backend', 'order' => 1, 'is_active' => true],
            ['name' => 'PHP', 'category' => 'backend', 'order' => 2, 'is_active' => true],
            ['name' => 'Node.js', 'category' => 'backend', 'order' => 3, 'is_active' => true],
            ['name' => 'Express', 'category' => 'backend', 'order' => 4, 'is_active' => true],
            ['name' => 'RESTful API', 'category' => 'backend', 'order' => 5, 'is_active' => true],
            ['name' => 'MySQL', 'category' => 'backend', 'order' => 6, 'is_active' => true],
            ['name' => 'PostgreSQL', 'category' => 'backend', 'order' => 7, 'is_active' => true],
            ['name' => 'SQLite', 'category' => 'backend', 'order' => 8, 'is_active' => true],
            ['name' => 'Redis', 'category' => 'backend', 'order' => 9, 'is_active' => true],

            // Tools
            ['name' => 'Git & GitHub', 'category' => 'tools', 'order' => 1, 'is_active' => true],
            ['name' => 'Docker', 'category' => 'tools', 'order' => 2, 'is_active' => true],
            ['name' => 'Postman', 'category' => 'tools', 'order' => 3, 'is_active' => true],
        ];

        foreach ($skills as $skill) {
            Skill::create($skill);
        }
    }
}
