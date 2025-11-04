<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'description' => $this->faker->sentence(12),
            'image' => $this->faker->imageUrl(600, 600, 'fashion', true),
            'parent_id' => null,
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 20),
        ];
    }
}
