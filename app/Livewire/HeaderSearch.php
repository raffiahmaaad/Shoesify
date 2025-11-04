<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

class HeaderSearch extends Component
{
    public string $query = '';

    #[Computed]
    public function suggestions(): Collection
    {
        if ($this->query === '') {
            return Collection::empty();
        }

        $term = Str::lower($this->query);

        return Product::query()
            ->select(['id', 'name', 'slug', 'price', 'discount', 'images', 'rating', 'reviews'])
            ->where('is_active', true)
            ->where(function ($query) use ($term): void {
                $query
                    ->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(short_description) LIKE ?', ["%{$term}%"]);
            })
            ->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->limit(3)
            ->get()
            ->map(function (Product $product): array {
                $primaryImage = collect($product->images ?? [])->filter()->first();

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->price,
                    'discount' => $product->discount,
                    'rating' => $product->rating,
                    'reviews' => $product->reviews,
                    'image' => $primaryImage,
                    'url' => route('products.show', $product->slug),
                ];
            });
    }

    public function submit(): void
    {
        if ($this->query === '') {
            return;
        }

        $this->dispatch('search-committed', query: $this->query);

        $this->redirectRoute('products.index', ['search' => $this->query], navigate: true);
    }

    public function render()
    {
        return view('livewire.header-search');
    }
}

