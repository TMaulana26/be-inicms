<?php

namespace Modules\Blog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Blog\Models\Post::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'category_id' => \Modules\Blog\Models\Category::factory(),
            'user_id' => \App\Models\User::factory(),
            'title' => $this->faker->sentence(),
            'slug' => \Illuminate\Support\Str::slug($this->faker->sentence()),
            'summary' => $this->faker->text(200),
            'content' => $this->faker->paragraphs(5, true),
            'status' => 'published',
            'is_featured' => $this->faker->boolean(20),
            'published_at' => now(),
        ];
    }
}
