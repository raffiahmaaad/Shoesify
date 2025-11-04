<?php

declare(strict_types=1);

namespace App\Livewire\Cart;

use App\Models\CartItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Indicator extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->count = $this->resolveCount();
    }

    #[On('cart-updated')]
    public function refreshCount(): void
    {
        $this->count = $this->resolveCount();
    }

    public function render()
    {
        $items = $this->previewItems();

        return view('livewire.cart.indicator', [
            'previewItems' => $items,
            'previewTotal' => (int) $items->sum(
                static fn (CartItem $item): int => (int) ($item->line_total ?? ($item->quantity * ($item->unit_price ?? 0))),
            ),
        ]);
    }

    protected function resolveCount(): int
    {
        if (! Auth::check()) {
            return (int) session('cart.items_count', 0);
        }

        return CartItem::query()
            ->whereHas('cart', fn ($query) => $query->where('user_id', Auth::id()))
            ->sum('quantity');
    }

    protected function previewItems(): Collection
    {
        if (! Auth::check()) {
            return collect();
        }

        return CartItem::query()
            ->with([
                'product:id,name,price,images',
                'variant:id,product_id,size,color_name,color_hex',
            ])
            ->whereHas('cart', fn ($query) => $query->where('user_id', Auth::id()))
            ->orderByDesc('updated_at')
            ->limit(4)
            ->get();
    }
}
