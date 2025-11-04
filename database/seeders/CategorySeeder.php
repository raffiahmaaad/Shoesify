<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $primary = collect([
            [
                'name' => 'Lifestyle',
                'description' => 'Sneaker harian dengan desain modern dan kenyamanan maksimal.',
            ],
            [
                'name' => 'Performance',
                'description' => 'Sepatu untuk performa tinggi, cocok untuk lari, gym, dan training.',
            ],
            [
                'name' => 'Trail & Outdoor',
                'description' => 'Sepatu tahan banting untuk petualangan alam dengan grip ekstra.',
            ],
            [
                'name' => 'Limited Edition',
                'description' => 'Rilis eksklusif dengan stok terbatas dan kolaborasi spesial.',
            ],
        ])->map(function (array $category, int $index) {
            return Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'image' => "https://images.unsplash.com/photo-1562157873-818bc0726f2e?auto=format&q=80&w=800&h=800&fit=crop&sat={$index}",
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        });

        $performance = $primary->firstWhere('slug', 'performance');
        if ($performance) {
            collect([
                'Running',
                'Training',
                'Basketball',
            ])->each(function (string $name, int $index) use ($performance): void {
                Category::updateOrCreate(
                    ['slug' => Str::slug("{$performance->slug}-{$name}")],
                    [
                        'name' => $name,
                        'description' => "{$name} footwear curated for peak performance.",
                        'parent_id' => $performance->id,
                        'image' => "https://images.unsplash.com/photo-1521412644187-c49fa049e84d?auto=format&q=80&w=800&h=800&fit=crop&sat={$index}",
                        'is_active' => true,
                        'sort_order' => $index + 1,
                    ]
                );
            });
        }
    }
}
