<?php

namespace Modules\Skill\Database\Seeders;

use Illuminate\Database\Seeder;

class SkillDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            SkillSeeder::class,
        ]);
    }
}
