<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ProductCatalog extends Component
{
    public string $search = '';

    /** @var array<int, string> */
    public array $selectedBrands = [];

    /** @var array<int, string> */
    public array $selectedSizes = [];

    /** @var array<int, string> */
    public array $selectedColors = [];

    public int $priceMin = 120;

    public int $priceMax = 260;

    public string $sort = 'popular';

    public string $view = 'grid';

    public int $perPage = 9;

    /** @var array<int, array<string, mixed>> */
    protected array $catalog = [];

    public function mount(): void
    {
        $this->catalog = [
            [
                'id' => 1,
                'name' => 'Flux Runner GTR',
                'brand' => 'Flux Labs',
                'price' => 198,
                'sizes' => ['39', '40', '41', '42'],
                'colors' => ['Teal', 'Black'],
                'swatches' => ['#0f172a', '#4de4d4'],
                'rating' => 4.9,
                'reviews' => 312,
                'badge' => 'Top Rated',
                'image' => 'https://images.unsplash.com/photo-1515955656352-a1fa3ffcd111?auto=format&q=80&w=1100',
                'terrain' => 'road',
                'in_stock' => true,
                'discount' => 20,
                'description' => 'Thermo-regulated mesh with kinetic outsole for explosive transitions.',
            ],
            [
                'id' => 2,
                'name' => 'Aero Knit Pulse',
                'brand' => 'Aero Forge',
                'price' => 169,
                'sizes' => ['38', '39', '40', '41', '42', '43'],
                'colors' => ['Yellow', 'Charcoal'],
                'swatches' => ['#fbbf24', '#0f1115'],
                'rating' => 4.8,
                'reviews' => 198,
                'badge' => 'Marathon Ready',
                'image' => 'https://images.unsplash.com/photo-1483721310020-03333e577078?auto=format&q=80&w=1100',
                'terrain' => 'road',
                'in_stock' => true,
                'discount' => 0,
                'description' => 'Breathable upper with responsive strike plate optimised for long distance.',
            ],
            [
                'id' => 3,
                'name' => 'Nebula Glide LX',
                'brand' => 'Nebula Works',
                'price' => 229,
                'sizes' => ['39', '40', '41', '42', '43'],
                'colors' => ['Sky', 'Obsidian'],
                'swatches' => ['#bae6fd', '#1e293b'],
                'rating' => 5.0,
                'reviews' => 421,
                'badge' => 'Carbon Plate',
                'image' => 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&q=80&w=1100',
                'terrain' => 'road',
                'in_stock' => true,
                'discount' => 15,
                'description' => 'Carbon lattice sole amplifies energy return with each stride.',
            ],
            [
                'id' => 4,
                'name' => 'Orbit Street 2.0',
                'brand' => 'Orbit Studio',
                'price' => 149,
                'sizes' => ['38', '39', '40', '41', '42'],
                'colors' => ['White', 'Midnight'],
                'swatches' => ['#f8fafc', '#0f172a'],
                'rating' => 4.7,
                'reviews' => 132,
                'badge' => 'Lifestyle',
                'image' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&q=80&w=1100',
                'terrain' => 'urban',
                'in_stock' => true,
                'discount' => 10,
                'description' => 'Low-top icon reimagined with dynamic cushioning pods.',
            ],
            [
                'id' => 5,
                'name' => 'Altitude Apex Trail',
                'brand' => 'Altitude Lab',
                'price' => 189,
                'sizes' => ['40', '41', '42', '43', '44'],
                'colors' => ['Olive', 'Stone'],
                'swatches' => ['#14532d', '#e2e8f0'],
                'rating' => 4.9,
                'reviews' => 252,
                'badge' => 'Trail',
                'image' => 'https://images.unsplash.com/photo-1504593811423-6dd665756598?auto=format&q=80&w=1100',
                'terrain' => 'trail',
                'in_stock' => true,
                'discount' => 0,
                'description' => 'Hyper-grip outsole and weatherproof membrane for multi-terrain control.',
            ],
            [
                'id' => 6,
                'name' => 'Velocity Phantom',
                'brand' => 'Flux Labs',
                'price' => 205,
                'sizes' => ['39', '40', '41', '42'],
                'colors' => ['Black', 'Infrared'],
                'swatches' => ['#0b0b0b', '#f87171'],
                'rating' => 4.6,
                'reviews' => 98,
                'badge' => 'Speed',
                'image' => 'https://images.unsplash.com/photo-1495107334309-fcf20504a5ab?auto=format&q=80&w=1100',
                'terrain' => 'track',
                'in_stock' => false,
                'discount' => 0,
                'description' => 'Feather-light chassis with traction fins for explosive takeoff.',
            ],
            [
                'id' => 7,
                'name' => 'Lunar Pulse Knit',
                'brand' => 'Nebula Works',
                'price' => 178,
                'sizes' => ['38', '39', '40', '41'],
                'colors' => ['Cream', 'Saffron'],
                'swatches' => ['#f5f5f4', '#f97316'],
                'rating' => 4.8,
                'reviews' => 164,
                'badge' => 'Studio',
                'image' => 'https://images.unsplash.com/photo-1512446816042-444d641267d4?auto=format&q=80&w=1100',
                'terrain' => 'studio',
                'in_stock' => true,
                'discount' => 5,
                'description' => 'Adaptive knit bootie hugs your foot for seamless studio sessions.',
            ],
            [
                'id' => 8,
                'name' => 'Quantum Drift Max',
                'brand' => 'Aero Forge',
                'price' => 239,
                'sizes' => ['41', '42', '43', '44'],
                'colors' => ['Graphite', 'Lime'],
                'swatches' => ['#1f2937', '#84cc16'],
                'rating' => 4.95,
                'reviews' => 310,
                'badge' => 'Elite',
                'image' => 'https://images.unsplash.com/photo-1521093470119-a3acdc43374a?auto=format&q=80&w=1100',
                'terrain' => 'road',
                'in_stock' => true,
                'discount' => 12,
                'description' => 'Dual-density foam with magnesium shank for high cadence athletes.',
            ],
            [
                'id' => 9,
                'name' => 'Mirage Flow V2',
                'brand' => 'Orbit Studio',
                'price' => 158,
                'sizes' => ['38', '39', '40', '41', '42'],
                'colors' => ['Lilac', 'Onyx'],
                'swatches' => ['#c4b5fd', '#111827'],
                'rating' => 4.5,
                'reviews' => 82,
                'badge' => 'Everyday',
                'image' => 'https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&q=80&w=1100',
                'terrain' => 'urban',
                'in_stock' => true,
                'discount' => 0,
                'description' => 'All-day comfort with memory foam collar and breathable mesh panels.',
            ],
            [
                'id' => 10,
                'name' => 'Summit Traverse GTX',
                'brand' => 'Altitude Lab',
                'price' => 248,
                'sizes' => ['41', '42', '43', '44', '45'],
                'colors' => ['Stone', 'Amber'],
                'swatches' => ['#d4d4d8', '#f59e0b'],
                'rating' => 4.92,
                'reviews' => 206,
                'badge' => 'Mountain',
                'image' => 'https://images.unsplash.com/photo-1595341888016-a392ef81b7de?auto=format&q=80&w=1100',
                'terrain' => 'trail',
                'in_stock' => true,
                'discount' => 0,
                'description' => 'Gore-Tex membrane with adaptive traction for alpine adventurers.',
            ],
            [
                'id' => 11,
                'name' => 'Ion Drift Lite',
                'brand' => 'Flux Labs',
                'price' => 142,
                'sizes' => ['38', '39', '40', '41'],
                'colors' => ['Ice', 'Midnight'],
                'swatches' => ['#e0f2fe', '#0f172a'],
                'rating' => 4.4,
                'reviews' => 54,
                'badge' => 'Essential',
                'image' => 'https://images.unsplash.com/photo-1539185441755-769473a23570?auto=format&q=80&w=1100',
                'terrain' => 'urban',
                'in_stock' => true,
                'discount' => 0,
                'description' => 'Lightweight essential built for effortless city commutes.',
            ],
            [
                'id' => 12,
                'name' => 'Pulse Reactor',
                'brand' => 'Aero Forge',
                'price' => 214,
                'sizes' => ['40', '41', '42', '43'],
                'colors' => ['Crimson', 'Slate'],
                'swatches' => ['#dc2626', '#334155'],
                'rating' => 4.85,
                'reviews' => 188,
                'badge' => 'Performance',
                'image' => 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?auto=format&q=80&w=1100',
                'terrain' => 'track',
                'in_stock' => true,
                'discount' => 18,
                'description' => 'Energy-return midsole with seamless upper for explosive sprints.',
            ],
        ];
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'selectedBrands', 'selectedSizes', 'selectedColors', 'sort'], true)) {
            $this->resetPagination();
        }
    }

    public function updatedPriceMin($value): void
    {
        $min = max(0, (int) $value);
        $this->priceMin = $min;
        if ($this->priceMin > $this->priceMax) {
            $this->priceMax = $this->priceMin;
        }
        $this->resetPagination();
    }

    public function updatedPriceMax($value): void
    {
        $max = max(0, (int) $value);
        $this->priceMax = $max;
        if ($this->priceMax < $this->priceMin) {
            $this->priceMin = $this->priceMax;
        }
        $this->resetPagination();
    }

    public function setView(string $layout): void
    {
        if (! in_array($layout, ['grid', 'list'], true)) {
            return;
        }

        $this->view = $layout;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->selectedBrands = [];
        $this->selectedSizes = [];
        $this->selectedColors = [];
        $this->priceMin = 120;
        $this->priceMax = 260;
        $this->sort = 'popular';
        $this->view = 'grid';
        $this->perPage = 9;
    }

    public function loadMore(): void
    {
        $this->perPage += 6;
    }

    #[Computed]
    public function brands(): array
    {
        return collect($this->catalog)
            ->pluck('brand')
            ->unique()
            ->values()
            ->all();
    }

    #[Computed]
    public function colors(): array
    {
        return collect($this->catalog)
            ->pluck('colors')
            ->flatten()
            ->unique()
            ->values()
            ->all();
    }

    #[Computed]
    public function sizes(): array
    {
        return collect($this->catalog)
            ->pluck('sizes')
            ->flatten()
            ->unique()
            ->values()
            ->sort()
            ->all();
    }

    #[Computed]
    public function filteredProducts(): Collection
    {
        $products = collect($this->catalog)
            ->filter(function (array $product): bool {
                if ($this->search !== '') {
                    $haystack = strtolower($product['name'] . ' ' . $product['brand'] . ' ' . $product['terrain']);
                    if (! str_contains($haystack, strtolower($this->search))) {
                        return false;
                    }
                }

                if ($this->selectedBrands !== [] && ! in_array($product['brand'], $this->selectedBrands, true)) {
                    return false;
                }

                if ($this->selectedColors !== [] && ! array_intersect($product['colors'], $this->selectedColors)) {
                    return false;
                }

                if ($this->selectedSizes !== [] && ! array_intersect($product['sizes'], $this->selectedSizes)) {
                    return false;
                }

                if ($product['price'] < $this->priceMin || $product['price'] > $this->priceMax) {
                    return false;
                }

                return true;
            });

        return $this->applySorting($products)->values();
    }

    public function render()
    {
        return view('livewire.product-catalog');
    }

    private function applySorting(Collection $products): Collection
    {
        return match ($this->sort) {
            'price_asc' => $products->sortBy('price'),
            'price_desc' => $products->sortByDesc('price'),
            'newest' => $products->sortByDesc('id'),
            default => $products->sortByDesc('rating'),
        };
    }

    private function resetPagination(): void
    {
        $this->perPage = 9;
    }
}
