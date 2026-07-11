<?php

namespace Modules\Portfolio\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Portfolio\Models\Project::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $titleEn = $this->faker->sentence(3);
        $titleId = $this->faker->sentence(3);

        return [
            'title' => [
                'en' => $titleEn,
                'id' => $titleId,
            ],
            'slug' => Str::slug($titleEn),
            'category' => $this->faker->randomElement(['FULLSTACK', 'FRONTEND', 'BACKEND']),
            'description' => [
                'en' => $this->faker->paragraph(),
                'id' => $this->faker->paragraph(),
            ],
            'tech_stack' => $this->faker->words(4),
            'github_url' => $this->faker->url(),
            'demo_url' => $this->faker->url(),
            'is_active' => true,
        ];
    }
}
