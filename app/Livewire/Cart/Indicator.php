<?php

declare(strict_types=1);

namespace App\Livewire\Cart;

use App\Models\CartItem;
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
        return view('livewire.cart.indicator');
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
}

