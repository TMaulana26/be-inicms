<?php

namespace Modules\Contact\Database\Seeders;

use Illuminate\Database\Seeder;

class ContactDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Modules\Contact\Models\ContactMessage::factory()->count(10)->create();
    }
}
