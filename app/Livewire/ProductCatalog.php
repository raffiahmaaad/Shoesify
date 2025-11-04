<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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

    public int $priceMin = 0;

    public int $priceMax = 0;

    public string $sort = 'popular';

    public string $view = 'grid';

    public int $perPage = 9;

    protected Collection $catalog;

    /** @var array{min:int,max:int} */
    protected array $priceBounds = ['min' => 0, 'max' => 0];

    public function mount(): void
    {
        $this->catalog = collect();
        $this->loadCatalog(initial: true);
    }

    public function hydrate(): void
    {
        $this->loadCatalog(initial: false);
    }

    protected function loadCatalog(bool $initial): void
    {
        $this->catalog = Product::query()
            ->with(['brand', 'variants'])
            ->where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (Product $product) => $this->mapProduct($product));

        $min = (int) ($this->catalog->min('price') ?? 0);
        $max = (int) ($this->catalog->max('price') ?? 0);

        $this->priceBounds = [
            'min' => $min,
            'max' => $max,
        ];

        if ($initial || ($this->priceMin === 0 && $this->priceMax === 0)) {
            $this->priceMin = $min;
            $this->priceMax = $max;
        } else {
            $this->priceMin = max($min, min($this->priceMin, $max));
            $this->priceMax = max($this->priceMin, min($this->priceMax, $max));
        }
    }

    protected function mapProduct(Product $product): array
    {
        $variants = $product->variants;

        $sizes = $variants->pluck('size')->filter()->unique()->sort()->values()->all();
        $colors = $variants->pluck('color_name')->filter()->unique()->values()->all();
        $swatches = $variants->pluck('color_hex')->filter()->unique()->values()->all();
        $stock = (int) $variants->sum('stock_quantity');

        return [
            'id' => $product->id,
            'slug' => $product->slug,
            'name' => $product->name,
            'brand' => $product->brand?->name ?? 'Shoesify Lab',
            'price' => (int) $product->price,
            'sizes' => $sizes,
            'colors' => $colors,
            'swatches' => $swatches,
            'rating' => (float) ($product->rating ?? 0),
            'reviews' => (int) ($product->reviews ?? 0),
            'badge' => $product->is_featured ? 'Featured' : null,
            'image' => collect($product->images ?? [])->filter()->first(),
            'in_stock' => $stock > 0,
            'discount' => (int) ($product->discount ?? 0),
            'description' => $product->short_description ?? Str::limit($product->description, 160),
        ];
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
        $this->priceMin = $this->priceBounds['min'] ?? 0;
        $this->priceMax = $this->priceBounds['max'] ?? 0;
    }

    #[Computed]
    public function brands(): array
    {
        return $this->catalog
            ->pluck('brand')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    #[Computed]
    public function sizes(): array
    {
        return $this->catalog
            ->flatMap(fn (array $product) => $product['sizes'] ?? [])
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    #[Computed]
    public function colorPalette(): array
    {
        return $this->catalog
            ->flatMap(function (array $product) {
                return collect($product['colors'] ?? [])
                    ->map(fn ($name, $index) => [
                        'name' => $name,
                        'hex' => $product['swatches'][$index] ?? '#d1d5db',
                    ]);
            })
            ->filter(fn (array $color) => filled($color['name']))
            ->unique(fn (array $color) => Str::lower($color['name']))
            ->values()
            ->all();
    }

    #[Computed]
    public function resultsCount(): int
    {
        return $this->filteredCatalog()->count();
    }

    #[Computed]
    public function visibleProducts(): array
    {
        return $this->filteredCatalog()
            ->take($this->perPage)
            ->values()
            ->all();
    }

    protected function filteredCatalog(): Collection
    {
        return $this->catalog
            ->when($this->search, function (Collection $items, string $term) {
                $term = Str::lower($term);

                return $items->filter(function (array $product) use ($term) {
                    $haystack = Str::lower($product['name'] . ' ' . $product['brand'] . ' ' . implode(' ', $product['colors'] ?? []));

                    return Str::contains($haystack, $term);
                });
            })
            ->when(! empty($this->selectedBrands), fn (Collection $items) => $items->whereIn('brand', $this->selectedBrands))
            ->when(! empty($this->selectedSizes), function (Collection $items) {
                return $items->filter(fn (array $product) => ! empty(array_intersect($product['sizes'], $this->selectedSizes)));
            })
            ->when(! empty($this->selectedColors), function (Collection $items) {
                return $items->filter(fn (array $product) => ! empty(array_intersect($product['colors'], $this->selectedColors)));
            })
            ->filter(function (array $product) {
                $price = $product['price'];

                return $price >= $this->priceMin && $price <= $this->priceMax;
            })
            ->when($this->sort === 'price_low', fn (Collection $items) => $items->sortBy('price'))
            ->when($this->sort === 'price_high', fn (Collection $items) => $items->sortByDesc('price'))
            ->when($this->sort === 'new', fn (Collection $items) => $items->sortByDesc('id'))
            ->when($this->sort === 'popular', fn (Collection $items) => $items->sortByDesc('rating'))
            ->values();
    }

    public function render()
    {
        return view('livewire.product-catalog');
    }
}
