<?php

namespace Modules\Page\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Page\Models\Page::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'title' => $this->faker->sentence(),
            'slug' => \Illuminate\Support\Str::slug($this->faker->sentence()),
            'content' => $this->faker->paragraphs(8, true),
            'status' => 'published',
        ];
    }
}
