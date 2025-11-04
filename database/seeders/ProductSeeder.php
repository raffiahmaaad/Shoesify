<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Flux Runner GTR',
                'category_slug' => 'performance-running',
                'brand_slug' => 'shoesify-lab',
                'price' => 198,
                'discount' => 20,
                'rating' => 4.9,
                'reviews' => 312,
                'release_date' => '2024-09-18',
                'short_description' => 'Runner flagship dengan kinetic outsole adaptif.',
                'description' => 'Flux Runner GTR dirancang untuk pelari urban yang menginginkan keseimbangan antara respons cepat dan stabilitas. Upper knit breathable dipadukan dengan plat energi untuk akselerasi instan.',
                'images' => [
                    'https://images.unsplash.com/photo-1515955656352-a1fa3ffcd111?auto=format&q=80&w=1200',
                    'https://images.unsplash.com/photo-1585533071988-98745a7ca63c?auto=format&q=80&w=1200',
                ],
                'variants' => [
                    ['size' => '39', 'color' => ['Arctic White', '#f8fafc'], 'stock' => 15],
                    ['size' => '41', 'color' => ['Obsidian Black', '#0f172a'], 'stock' => 12],
                    ['size' => '42', 'color' => ['Obsidian Black', '#0f172a'], 'stock' => 8],
                ],
                'featured' => true,
            ],
            [
                'name' => 'Aero Knit Pulse',
                'category_slug' => 'performance-running',
                'brand_slug' => 'velocity-works',
                'price' => 169,
                'discount' => 0,
                'rating' => 4.8,
                'reviews' => 198,
                'release_date' => '2024-07-02',
                'short_description' => 'Upper knit breathable dengan strike plate responsif.',
                'description' => 'Aero Knit Pulse dibuat untuk maraton jauh. Struktur knit multizona menjaga kaki tetap sejuk, sementara midsole twin-density menjaga langkah tetap ringan.',
                'images' => [
                    'https://images.unsplash.com/photo-1483721310020-03333e577078?auto=format&q=80&w=1200',
                    'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&q=80&w=1200',
                ],
                'variants' => [
                    ['size' => '38', 'color' => ['Sunrise Yellow', '#facc15'], 'stock' => 10],
                    ['size' => '40', 'color' => ['Midnight', '#111827'], 'stock' => 14],
                    ['size' => '42', 'color' => ['Midnight', '#111827'], 'stock' => 9],
                ],
                'featured' => true,
            ],
            [
                'name' => 'Nebula Glide LX',
                'category_slug' => 'limited-edition',
                'brand_slug' => 'shoesify-lab',
                'price' => 229,
                'discount' => 15,
                'rating' => 5.0,
                'reviews' => 421,
                'release_date' => '2024-08-11',
                'short_description' => 'Carbon lattice sole meningkatkan dorongan 18% setiap langkah.',
                'description' => 'Nebula Glide LX memadukan carbon lattice sole dan panel reflective untuk lari malam. Dirilis terbatas dengan nomor seri khusus.',
                'images' => [
                    'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&q=80&w=1200',
                    'https://images.unsplash.com/photo-1575537302964-96cd47c06b1f?auto=format&q=80&w=1200',
                ],
                'variants' => [
                    ['size' => '39', 'color' => ['Glacier Blue', '#bae6fd'], 'stock' => 6],
                    ['size' => '41', 'color' => ['Storm Navy', '#1e3a8a'], 'stock' => 5],
                    ['size' => '43', 'color' => ['Storm Navy', '#1e3a8a'], 'stock' => 3],
                ],
                'featured' => true,
            ],
            [
                'name' => 'Orbit Street 2.0',
                'category_slug' => 'lifestyle',
                'brand_slug' => 'studio-nine',
                'price' => 149,
                'discount' => 10,
                'rating' => 4.7,
                'reviews' => 132,
                'release_date' => '2024-06-25',
                'short_description' => 'Silhouette low-top ikonik dengan cushioning pods dinamis.',
                'description' => 'Orbit Street 2.0 hadir dengan panel suede lembut dan outsole translucent. Dirancang untuk city cruising dengan style clean.',
                'images' => [
                    'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&q=80&w=1200',
                    'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?auto=format&q=80&w=1200',
                ],
                'variants' => [
                    ['size' => '40', 'color' => ['Chalk', '#f1f5f9'], 'stock' => 18],
                    ['size' => '42', 'color' => ['Night Slate', '#0f172a'], 'stock' => 16],
                ],
                'featured' => false,
            ],
            [
                'name' => 'Altitude Apex Trail',
                'category_slug' => 'trail-outdoor',
                'brand_slug' => 'altitude-co',
                'price' => 189,
                'discount' => 0,
                'rating' => 4.9,
                'reviews' => 252,
                'release_date' => '2024-04-30',
                'short_description' => 'Hyper-grip outsole dengan membran waterproof.',
                'description' => 'Altitude Apex Trail dibekali Vibram outsole dan lapisan eVent untuk menjaga kaki tetap kering di segala medan.',
                'images' => [
                    'https://images.unsplash.com/photo-1504593811423-6dd665756598?auto=format&q=80&w=1200',
                    'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&q=80&w=1200',
                ],
                'variants' => [
                    ['size' => '41', 'color' => ['Evergreen', '#14532d'], 'stock' => 11],
                    ['size' => '43', 'color' => ['Granite', '#475569'], 'stock' => 9],
                    ['size' => '44', 'color' => ['Granite', '#475569'], 'stock' => 7],
                ],
                'featured' => false,
            ],
            [
                'name' => 'Pulse React V3',
                'category_slug' => 'performance-training',
                'brand_slug' => 'velocity-works',
                'price' => 210,
                'discount' => 5,
                'rating' => 4.8,
                'reviews' => 274,
                'release_date' => '2024-05-20',
                'short_description' => 'Midsole reaktif dengan energy rods untuk akselerasi maksimal.',
                'description' => 'Pulse React V3 mengusung energy rods generasi ketiga yang merespons setiap tekanan kaki. Cocok untuk HIIT maupun sprint.',
                'images' => [
                    'https://images.unsplash.com/photo-1529927066849-66e1d4d44ad3?auto=format&q=80&w=1200',
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&q=80&w=1200',
                ],
                'variants' => [
                    ['size' => '40', 'color' => ['Infrared', '#ef4444'], 'stock' => 10],
                    ['size' => '41', 'color' => ['Graphite', '#1f2937'], 'stock' => 8],
                    ['size' => '42', 'color' => ['Graphite', '#1f2937'], 'stock' => 6],
                ],
                'featured' => true,
            ],
            [
                'name' => 'Horizon Terra GTX',
                'category_slug' => 'trail-outdoor',
                'brand_slug' => 'altitude-co',
                'price' => 240,
                'discount' => 12,
                'rating' => 4.9,
                'reviews' => 186,
                'release_date' => '2024-08-28',
                'short_description' => 'Sepatu petualang dengan membran Gore-Tex dan outsole Vibram.',
                'description' => 'Horizon Terra GTX siap menghadapi rute mountain ridge sampai jalur berlumpur dengan perlindungan maksimal.',
                'images' => [
                    'https://images.unsplash.com/photo-1520614073990-dd60228cc061?auto=format&q=80&w=1200',
                    'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&q=80&w=1200',
                ],
                'variants' => [
                    ['size' => '41', 'color' => ['Canyon Clay', '#f97316'], 'stock' => 6],
                    ['size' => '43', 'color' => ['Night Forest', '#065f46'], 'stock' => 5],
                ],
                'featured' => false,
            ],
            [
                'name' => 'Nova Knit Lite',
                'category_slug' => 'lifestyle',
                'brand_slug' => 'shoesify-lab',
                'price' => 132,
                'discount' => 0,
                'rating' => 4.5,
                'reviews' => 120,
                'release_date' => '2024-01-05',
                'short_description' => 'Knit ultra ringan dengan outsole foam untuk pemakaian harian.',
                'description' => 'Nova Knit Lite menawarkan kenyamanan sepanjang hari dengan upper knit tanpa jahitan dan insole memory foam.',
                'images' => [
                    'https://images.unsplash.com/photo-1527169402691-feff5539e52c?auto=format&q=80&w=1200',
                    'https://images.unsplash.com/photo-1512499617640-c2f999098c01?auto=format&q=80&w=1200',
                ],
                'variants' => [
                    ['size' => '36', 'color' => ['Sky Blue', '#38bdf8'], 'stock' => 12],
                    ['size' => '38', 'color' => ['Soft Pearl', '#e0f2fe'], 'stock' => 11],
                    ['size' => '40', 'color' => ['Soft Pearl', '#e0f2fe'], 'stock' => 9],
                ],
                'featured' => false,
            ],
            [
                'name' => 'Drift Runner SE',
                'category_slug' => 'limited-edition',
                'brand_slug' => 'flux-labs',
                'price' => 205,
                'discount' => 18,
                'rating' => 4.8,
                'reviews' => 267,
                'release_date' => '2024-09-03',
                'short_description' => 'Stabilisasi multi-directional dengan midsole nitrogen-infused.',
                'description' => 'Drift Runner SE hadir dengan cushioning nitrogen dan cage stabilizer transparan untuk kontrol maksimal.',
                'images' => [
                    'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&q=80&w=1200',
                    'https://images.unsplash.com/photo-1516738901171-8eb4fc13bd20?auto=format&q=80&w=1200',
                ],
                'variants' => [
                    ['size' => '40', 'color' => ['Cobalt', '#1d4ed8'], 'stock' => 7],
                    ['size' => '42', 'color' => ['Slate', '#334155'], 'stock' => 6],
                ],
                'featured' => true,
            ],
        ];

        foreach ($products as $entry) {
            $category = Category::where('slug', $entry['category_slug'])->first()
                ?? Category::where('slug', Str::slug($entry['category_slug']))->first();
            $brand = Brand::where('slug', $entry['brand_slug'])->first();

            if (! $category || ! $brand) {
                continue;
            }

            $slug = Str::slug($entry['name']);
            $comparePrice = $entry['discount'] > 0
                ? (int) round($entry['price'] / (1 - ($entry['discount'] / 100)))
                : null;

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'name' => $entry['name'],
                    'sku' => strtoupper(Str::random(10)),
                    'short_description' => $entry['short_description'],
                    'description' => $entry['description'],
                    'price' => $entry['price'],
                    'compare_price' => $comparePrice,
                    'cost_price' => (int) ($entry['price'] * 0.6),
                    'discount' => $entry['discount'],
                    'rating' => $entry['rating'],
                    'reviews' => $entry['reviews'],
                    'images' => $entry['images'],
                    'meta_title' => $entry['name'] . ' | Shoesify',
                    'meta_description' => Str::limit($entry['description'], 160),
                    'release_date' => Carbon::parse($entry['release_date']),
                    'is_active' => true,
                    'is_featured' => $entry['featured'],
                ]
            );

            $variantData = Arr::get($entry, 'variants', []);
            $product->variants()->delete();

            foreach ($variantData as $variant) {
                $color = $variant['color'] ?? [null, null];

                ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => strtoupper(Str::slug($product->slug . '-' . $variant['size'] . '-' . ($color[0] ?? ''))) . Str::random(4),
                    'size' => $variant['size'] ?? null,
                    'color_name' => $color[0] ?? null,
                    'color_hex' => $color[1] ?? null,
                    'stock_quantity' => $variant['stock'] ?? 0,
                    'price_adjustment' => 0,
                    'images' => $product->images,
                ]);
            }
        }
    }
}
