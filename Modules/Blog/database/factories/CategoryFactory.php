<?php

namespace Modules\Blog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Blog\Models\Category::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word();
        return [
            'name' => $this->faker->sentence(2),
            'slug' => \Illuminate\Support\Str::slug($name),
            'description' => $this->faker->paragraph(),
            'is_active' => true,
        ];
    }
}
