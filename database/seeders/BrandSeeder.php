<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Shoesify Lab',
                'description' => 'Tim desain internal Shoesify dengan fokus pada teknologi motion-adaptive.',
            ],
            [
                'name' => 'Flux Labs',
                'description' => 'Brand futuristik dengan signature cushioning dan material karbon.',
            ],
            [
                'name' => 'Altitude Co.',
                'description' => 'Spesialis sepatu outdoor dengan waterproof membrane dan daya cengkeram tinggi.',
            ],
            [
                'name' => 'Velocity Works',
                'description' => 'Dibuat untuk runner profesional, fokus pada energy return maksimal.',
            ],
            [
                'name' => 'Studio Nine',
                'description' => 'Sepatu lifestyle dengan premium knit upper berbasis sustainable material.',
            ],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['slug' => Str::slug($brand['name'])],
                [
                    'name' => $brand['name'],
                    'description' => $brand['description'],
                    'logo' => 'https://images.unsplash.com/photo-1618005198919-d3d4b5a92eee?auto=format&q=80&w=480&h=240&fit=crop',
                    'is_featured' => in_array($brand['name'], ['Shoesify Lab', 'Flux Labs'], true),
                    'is_active' => true,
                ]
            );
        }
    }
}
