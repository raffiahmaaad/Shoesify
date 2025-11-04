<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        $basePrice = $this->faker->numberBetween(120, 320);
        $discount = $this->faker->boolean(35) ? $this->faker->numberBetween(5, 25) : 0;

        $category = Category::query()->inRandomOrder()->first() ?? Category::factory()->create();
        $brand = Brand::query()->inRandomOrder()->first() ?? Brand::factory()->create();

        $imageUrls = [
            $this->faker->imageUrl(900, 900, 'fashion', true),
            $this->faker->imageUrl(900, 900, 'fashion', true),
        ];

        return [
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => Str::title($name),
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'sku' => strtoupper(Str::random(10)),
            'short_description' => $this->faker->sentence(10),
            'description' => $this->faker->paragraphs(3, true),
            'price' => $basePrice,
            'compare_price' => $discount > 0 ? (int) round($basePrice / (1 - $discount / 100)) : null,
            'cost_price' => (int) ($basePrice * 0.6),
            'discount' => $discount,
            'rating' => $this->faker->randomFloat(1, 4.3, 5.0),
            'reviews' => $this->faker->numberBetween(25, 450),
            'images' => $imageUrls,
            'meta_title' => Str::title($name) . ' | Shoesify',
            'meta_description' => $this->faker->sentence(18),
            'release_date' => $this->faker->dateTimeBetween('-9 months', 'now'),
            'is_active' => true,
            'is_featured' => $this->faker->boolean(30),
        ];
    }
}
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        $basePrice = $this->faker->numberBetween(120, 280);
        $discount = $this->faker->boolean(30) ? $this->faker->numberBetween(5, 25) : 0;

        $availableSizes = collect(range(36, 45))->map(fn (int $size) => (string) $size);
        $sizes = $availableSizes->shuffle()->take($this->faker->numberBetween(3, 6))->values()->all();

        $palette = collect([
            ['name' => 'Onyx', 'hex' => '#0f172a'],
            ['name' => 'Mist', 'hex' => '#e2e8f0'],
            ['name' => 'Aurora', 'hex' => '#4de4d4'],
            ['name' => 'Solar', 'hex' => '#facc15'],
            ['name' => 'Nebula', 'hex' => '#7c3aed'],
            ['name' => 'Crimson', 'hex' => '#ef4444'],
        ]);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'brand' => $this->faker->randomElement(['Shoesify Lab', 'Flux Labs', 'Altitude Co.', 'Velocity Works', 'Studio Nine']),
            'description' => $this->faker->sentences(2, true),
            'price' => $basePrice,
            'discount' => $discount,
            'rating' => $this->faker->randomFloat(1, 4.3, 5.0),
            'reviews' => $this->faker->numberBetween(45, 520),
            'image' => $this->faker->imageUrl(900, 900, 'fashion', true),
            'release_date' => $this->faker->dateTimeBetween('-9 months', 'now'),
            'sizes' => $sizes,
            'colors' => $palette->shuffle()->take($this->faker->numberBetween(1, 3))->values()->all(),
            'is_featured' => $this->faker->boolean(40),
        ];
    }
}
