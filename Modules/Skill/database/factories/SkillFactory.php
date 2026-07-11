<?php

namespace Modules\Skill\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Skill\Models\Skill::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'category' => $this->faker->randomElement(['frontend', 'backend', 'tools']),
            'order' => $this->faker->numberBetween(0, 100),
            'is_active' => true,
        ];
    }
}
