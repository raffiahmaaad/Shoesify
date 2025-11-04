<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Component;

class Catalog extends Component
{
    public ?string $search = '';

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

    protected Collection $catalog;

    public const CACHE_KEY = 'catalog.products';
    private const CACHE_TTL = 900;

    public function mount(?string $search = null): void
    {
        $this->search = $search ?? '';

        $this->catalog = Cache::remember(
            self::CACHE_KEY,
            now()->addSeconds(self::CACHE_TTL),
            function (): Collection {
                return Product::query()
                    ->with(['brand', 'variants'])
                    ->orderByDesc('release_date')
                    ->get()
                    ->map(function (Product $product): array {
                        $primaryImage = collect($product->images)->filter()->first();
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
                        ];
                    });
            }
        );

        $min = (int) ($this->catalog->min('price') ?? 0);
        $max = (int) ($this->catalog->max('price') ?? 0);

        $this->priceBounds = ['min' => $min, 'max' => $max];
        $this->priceMin = $min;
        $this->priceMax = $max;
    }

    public function updated(string $property): void
    {
        if (Str::startsWith($property, ['search', 'selectedBrands', 'selectedSizes', 'selectedColors', 'priceMin', 'priceMax', 'sort'])) {
            $this->perPage = 9;
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
        $this->selectedBrands = [];
        $this->selectedSizes = [];
        $this->selectedColors = [];
        $this->sort = 'popular';
        $this->view = 'grid';
        $this->perPage = 9;
        $this->priceMin = $this->priceBounds['min'];
        $this->priceMax = $this->priceBounds['max'];
    }

    public function getBrandsProperty(): array
    {
        return $this->catalog
            ->pluck('brand')
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function getSizesProperty(): array
    {
        return $this->catalog
            ->flatMap(fn (array $product) => $product['sizes'])
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function getColorPaletteProperty(): array
    {
        return $this->catalog
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

    protected function filteredCatalog(): Collection
    {
        $collection = collect($this->catalog->all());

        if (($this->search ?? '') !== '') {
            $needle = Str::lower((string) $this->search);
            $collection = $collection->filter(function (array $product) use ($needle): bool {
                $haystack = Str::of(
                    implode(' ', [
                        $product['name'],
                        $product['brand'],
                        $product['description'],
                    ])
                )->lower()->value();

                return Str::contains($haystack, $needle);
            });
        }

        if ($this->selectedBrands !== []) {
            $collection = $collection->whereIn('brand', $this->selectedBrands);
        }

        if ($this->selectedSizes !== []) {
            $sizes = $this->selectedSizes;
            $collection = $collection->filter(fn (array $product): bool => ! empty(array_intersect($product['sizes'], $sizes)));
        }

        if ($this->selectedColors !== []) {
            $selected = collect($this->selectedColors)->map(fn ($color) => Str::lower($color));
            $collection = $collection->filter(function (array $product) use ($selected): bool {
                $productColors = collect($product['colors'])->pluck('name')->map(fn ($name) => Str::lower($name));
                return $productColors->intersect($selected)->isNotEmpty();
            });
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
