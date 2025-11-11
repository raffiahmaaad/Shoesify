<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

class Catalog extends Component
{
    public ?string $search = '';
    public ?string $category = null;

    /**
     * @var array<int, string>
     */
    public array $selectedBrands = [];

    /**
     * @var array<int, string>
     */
    public array $selectedSizes = [];

    /**
     * @var array<int, string>
     */
    public array $selectedColors = [];

    public int $priceMin = 0;
    public int $priceMax = 0;
    public string $sort = 'popular';
    public string $view = 'grid';
    public int $perPage = 9;

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => null],
    ];

    /**
     * @var array<string, string>
     */
    public array $sortOptions = [
        'popular' => 'Paling Populer',
        'new' => 'Terbaru',
        'price_low' => 'Harga: Rendah ke Tinggi',
        'price_high' => 'Harga: Tinggi ke Rendah',
    ];

    protected array $priceBounds = ['min' => 0, 'max' => 0];

    protected ?Collection $catalog = null;

    public const CACHE_KEY = 'catalog.products';
    private const CACHE_TTL = 300; // Kurangi TTL ke 5 menit untuk development

    protected bool $debug = true; // Tambahkan mode debug

    public function boot()
    {
        $this->loadCatalog();
    }

    public function mount(?string $search = null, ?string $category = null): void
    {
        $this->search = $search ?? '';
        $this->category = $category ?: $this->category;

        if (!$this->catalog) {
            $this->loadCatalog();
        }
    }

    protected function loadCatalog(): void
    {
        // Load products dengan eager loading yang tepat
        $this->catalog = Cache::remember(
            self::CACHE_KEY,
            now()->addSeconds(self::CACHE_TTL),
            function (): Collection {
                $query = Product::query()
                    ->with([
                        'brand',
                        'category',
                        'variants' => function($query) {
                            $query->where('stock_quantity', '>', 0);
                        }
                    ]);

                // Filter by stock availability
                $query->whereHas('variants', function($query) {
                    $query->where('stock_quantity', '>', 0);
                });

                return $query->orderByDesc('release_date')
                    ->get()
                    ->map(function (Product $product): array {
                        $primaryImage = collect($product->images ?? [])->filter()->first();
                        $sizes = $product->variants
                            ->pluck('size')
                            ->filter()
                            ->unique()
                            ->values()
                            ->all();

                        $colors = $product->variants
                            ->map(fn ($variant) => [
                                'name' => $variant->color_name,
                                'hex' => $variant->color_hex ?? '#f4f4f5',
                            ])
                            ->filter(fn ($color) => filled($color['name']))
                            ->unique(fn ($color) => Str::lower($color['name']))
                            ->values()
                            ->all();

                        $stockSum = (int) $product->variants->sum('stock_quantity');

                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'slug' => $product->slug,
                            'brand' => optional($product->brand)->name ?? 'Unknown',
                            'description' => $product->short_description ?? Str::limit($product->description, 120),
                            'price' => (int) $product->price,
                            'discount' => (int) $product->discount,
                            'rating' => (float) $product->rating,
                            'reviews' => (int) $product->reviews,
                            'image' => $primaryImage,
                            'sizes' => $sizes,
                            'colors' => $colors,
                            'release_date' => optional($product->release_date)->toDateString(),
                            'is_featured' => (bool) $product->is_featured,
                            'category_name' => optional($product->category)->name,
                            'category_slug' => optional($product->category)->slug,
                            'stock' => $stockSum,
                            'in_stock' => $stockSum > 0,
                        ];
                    });
            }
        );

        $this->refreshPriceBounds();
    }

    protected function refreshPriceBounds(): void
    {
        $scoped = $this->scopedCatalog();

        if ($scoped->isEmpty()) {
            $this->priceBounds = ['min' => 0, 'max' => 0];
            $this->priceMin = 0;
            $this->priceMax = 0;

            return;
        }

        $min = (int) ($scoped->min('price') ?? 0);
        $max = (int) ($scoped->max('price') ?? 0);

        $this->priceBounds = ['min' => $min, 'max' => $max];
        $this->priceMin = $min;
        $this->priceMax = $max;
    }

    public function updated(string $property): void
    {
        if (Str::startsWith($property, ['search', 'selectedBrands', 'selectedSizes', 'selectedColors', 'priceMin', 'priceMax', 'sort'])) {
            $this->perPage = 9;
        }

        if ($property === 'category') {
            $this->perPage = 9;
            $this->refreshPriceBounds();
        }
    }

    public function updatedPriceMin(): void
    {
        if ($this->priceMin > $this->priceMax) {
            $this->priceMax = $this->priceMin;
        }
    }

    public function updatedPriceMax(): void
    {
        if ($this->priceMax < $this->priceMin) {
            $this->priceMin = $this->priceMax;
        }
    }

    public function toggleView(string $view): void
    {
        if (in_array($view, ['grid', 'list'], true)) {
            $this->view = $view;
        }
    }

    public function loadMore(): void
    {
        $this->perPage = min($this->perPage + 6, $this->filteredCatalog()->count());
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->category = null;
        $this->selectedBrands = [];
        $this->selectedSizes = [];
        $this->selectedColors = [];
        $this->sort = 'popular';
        $this->view = 'grid';
        $this->perPage = 9;

        // Reset price bounds
        $this->loadCatalog();
        $this->refreshPriceBounds();

        if ($this->debug) {
            Log::info('Filters reset', [
                'catalog_count' => $this->catalog?->count(),
                'price_bounds' => $this->priceBounds
            ]);
        }
    }

    public function getBrandsProperty(): array
    {
        return $this->scopedCatalog()
            ->pluck('brand')
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function getSizesProperty(): array
    {
        return $this->scopedCatalog()
            ->flatMap(fn (array $product) => $product['sizes'])
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function getColorPaletteProperty(): array
    {
        return $this->scopedCatalog()
            ->flatMap(fn (array $product) => $product['colors'])
            ->unique(fn (array $color) => Str::lower($color['name']))
            ->values()
            ->all();
    }

    public function getResultsCountProperty(): int
    {
        return $this->filteredCatalog()->count();
    }

    public function getVisibleProductsProperty(): array
    {
        return $this->filteredCatalog()->take($this->perPage)->all();
    }

    public function getHasMoreProperty(): bool
    {
        return $this->filteredCatalog()->count() > $this->perPage;
    }

    public function getPriceBoundsProperty(): array
    {
        return $this->priceBounds;
    }

    public function render()
    {
        return view('livewire.products.catalog', [
            'brands' => $this->brands,
            'sizes' => $this->sizes,
            'colorPalette' => $this->colorPalette,
            'resultsCount' => $this->resultsCount,
            'visibleProducts' => $this->visibleProducts,
            'hasMore' => $this->hasMore,
            'priceBounds' => $this->priceBounds,
        ]);
    }

    protected function scopedCatalog(): Collection
    {
        if (is_null($this->catalog)) {
            $this->loadCatalog();
        }

        if ($this->debug) {
            Log::info('Catalog debug', [
                'catalog_count' => $this->catalog?->count(),
                'category' => $this->category,
                'search' => $this->search,
                'filter_counts' => [
                    'brands' => count($this->selectedBrands),
                    'sizes' => count($this->selectedSizes),
                    'colors' => count($this->selectedColors),
                ],
                'price_range' => [$this->priceMin, $this->priceMax],
            ]);
        }

        if (!$this->catalog) {
            return collect([]);
        }

        $items = $this->catalog->all();

        return collect($items)
            ->filter(function (array $product): bool {
                if (! $this->category) {
                    return true;
                }

                return Str::lower($product['category_slug'] ?? '') === Str::lower($this->category);
            })
            ->values();
    }

    protected function filteredCatalog(): Collection
    {
        if (!$this->catalog) {
            return collect([]);
        }

        $collection = $this->scopedCatalog();

        // Logging untuk debug
        if ($this->debug) {
            Log::info('Initial collection count', ['count' => $collection->count()]);
        }

        // Filter by search
        if (filled($this->search)) {
            $needle = Str::lower((string) $this->search);
            $collection = $collection->filter(function (array $product) use ($needle): bool {
                return Str::contains(
                    Str::lower($product['name'] . ' ' . $product['brand']),
                    $needle
                );
            });

            if ($this->debug) {
                Log::info('After search filter', [
                    'search' => $this->search,
                    'count' => $collection->count()
                ]);
            }
        }

        // Filter by brands
        if (!empty($this->selectedBrands)) {
            $selectedBrands = collect($this->selectedBrands)->map(fn ($brand) => Str::lower($brand));
            $collection = $collection->filter(function (array $product) use ($selectedBrands): bool {
                return $selectedBrands->contains(Str::lower($product['brand']));
            });

            if ($this->debug) {
                Log::info('After brand filter', [
                    'selected_brands' => $this->selectedBrands,
                    'count' => $collection->count()
                ]);
            }
        }

        // Filter by sizes
        if (!empty($this->selectedSizes)) {
            $collection = $collection->filter(function (array $product): bool {
                return collect($product['sizes'])
                    ->intersect($this->selectedSizes)
                    ->isNotEmpty();
            });

            if ($this->debug) {
                Log::info('After size filter', [
                    'selected_sizes' => $this->selectedSizes,
                    'count' => $collection->count()
                ]);
            }
        }

        // Filter by colors
        if (!empty($this->selectedColors)) {
            $selected = collect($this->selectedColors)->map(fn ($color) => Str::lower($color));
            $collection = $collection->filter(function (array $product) use ($selected): bool {
                $productColors = collect($product['colors'])
                    ->pluck('name')
                    ->map(fn ($name) => Str::lower($name));
                return $productColors->intersect($selected)->isNotEmpty();
            });

            if ($this->debug) {
                Log::info('After color filter', [
                    'selected_colors' => $this->selectedColors,
                    'count' => $collection->count()
                ]);
            }
        }

        // Filter by price range
        if ($this->priceMin > 0 || $this->priceMax > 0) {
            $collection = $collection->filter(function (array $product): bool {
                $price = $product['price'];
                $min = $this->priceMin ?: 0;
                $max = $this->priceMax ?: PHP_INT_MAX;
                return $price >= $min && $price <= $max;
            });

            if ($this->debug) {
                Log::info('After price filter', [
                    'price_range' => [$this->priceMin, $this->priceMax],
                    'count' => $collection->count()
                ]);
            }
        }

        $collection = $collection->filter(function (array $product): bool {
            $price = $product['price'];
            return $price >= $this->priceMin && $price <= $this->priceMax;
        });

        $collection = match ($this->sort) {
            'price_low' => $collection->sortBy('price'),
            'price_high' => $collection->sortByDesc('price'),
            'new' => $collection->sortByDesc('release_date'),
            default => $collection->sortByDesc(fn (array $product) => ($product['rating'] * 1000) + $product['reviews']),
        };

        return $collection->values();
    }
}
