<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->company;

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'logo' => $this->faker->imageUrl(320, 160, 'business', true),
            'description' => $this->faker->sentence(15),
            'is_featured' => $this->faker->boolean(40),
            'is_active' => true,
        ];
    }
}
