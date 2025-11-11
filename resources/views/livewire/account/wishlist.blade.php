<?php

use App\Models\Wishlist;
use App\Services\CartManager;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    // Livewire serialization doesn't support typed public properties in this context,
    // use untyped properties so Livewire can hydrate/serialize correctly.
    public $items = [];
    public $cartManager;

    public function mount(CartManager $cartManager): void
    {
        $this->cartManager = $cartManager;
        $this->refreshList();
    }

    public function refreshList(): void
    {
        $this->items = Auth::user()
            ->wishlists()
            ->with([
                'product.brand',
                'variant',
            ])
            ->latest()
            ->get()
            ->map(function (Wishlist $wishlist): array {
                $product = $wishlist->product;
                $variant = $wishlist->variant;
                $image = collect($product?->images ?? [])->first();

                return [
                    'id' => $wishlist->id,
                    'product_id' => $product?->id,
                    'product_name' => $product?->name ?? 'Produk tidak tersedia',
                    'product_slug' => $product?->slug,
                    'brand' => $product?->brand?->name,
                    'price' => (int) ($product?->price ?? 0),
                    'image' => $image,
                    'variant' => $variant ? trim(collect([$variant->color_name, $variant->size ? 'EU '.$variant->size : null])->filter()->implode(' • ')) : null,
                    'variant_id' => $variant?->id,
                ];
            })
            ->all();
    }

    public function remove(int $wishlistId): void
    {
        Auth::user()->wishlists()->where('id', $wishlistId)->delete();
        $this->refreshList();
    }

    public function moveToCart(int $wishlistId): void
    {
        $wishlist = Auth::user()->wishlists()->where('id', $wishlistId)->with('product')->first();
        if (! $wishlist || ! $wishlist->product) {
            return;
        }

        $cart = $this->cartManager->current();
        $cart->items()->create([
            'product_id' => $wishlist->product_id,
            'product_variant_id' => $wishlist->product_variant_id,
            'quantity' => 1,
            'unit_price' => (int) $wishlist->product->price,
            'line_total' => (int) $wishlist->product->price,
            'metadata' => [
                'source' => 'wishlist',
            ],
        ]);

        $this->cartManager->refreshTotals($cart);
        $wishlist->delete();
        $this->refreshList();
        $this->dispatch('cart-updated');
    }
}; ?>

<x-account.layout title="Wishlist" subtitle="Produk favoritmu tetap tersimpan di sini. Pindahkan ke keranjang kapan saja.">
    @if (empty($items))
        <div class="glass-panel space-y-3 p-8 text-center text-white/60">
            <h2 class="text-lg font-semibold text-white">Wishlist masih kosong</h2>
            <p class="text-sm text-white/60">Tambahkan produk dengan menekan ikon hati di halaman katalog atau detail produk.</p>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-5 py-2 text-sm font-semibold text-white transition hover:bg-white/15" wire:navigate>
                Jelajahi produk
                <span aria-hidden="true">→</span>
            </a>
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($items as $item)
                <article class="glass-panel space-y-4 p-5" wire:key="wishlist-item-{{ $item['id'] }}">
                    <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-white/5">
                        @if ($item['image'])
                            <img src="{{ $item['image'] }}" alt="{{ $item['product_name'] }}" class="h-48 w-full object-cover">
                        @else
                            <div class="flex h-48 items-center justify-center text-sm text-white/50">No image</div>
                        @endif
                        <button type="button" class="absolute right-3 top-3 inline-flex h-9 w-9 items-center justify-center rounded-full bg-neutral-950/70 text-white transition hover:bg-rose-500/70" wire:click="remove({{ $item['id'] }})">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m15 9-6 6m0-6 6 6" />
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-2 text-sm text-white/70">
                        <a href="{{ $item['product_slug'] ? route('products.show', $item['product_slug']) : '#' }}" class="text-base font-semibold text-white transition hover:text-[#4de4d4]" wire:navigate>
                            {{ $item['product_name'] }}
                        </a>
                        @if ($item['brand'])
                            <p class="text-xs text-white/50 uppercase tracking-[0.3em]">{{ $item['brand'] }}</p>
                        @endif
                        @if ($item['variant'])
                            <p class="text-xs text-white/50">{{ $item['variant'] }}</p>
                        @endif
                        <p class="font-semibold text-white">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-3 text-xs text-white/60">
                        <button type="button" class="inline-flex items-center gap-2 rounded-full bg-[#4de4d4] px-4 py-2 text-sm font-semibold text-neutral-900 transition hover:-translate-y-0.5" wire:click="moveToCart({{ $item['id'] }})">
                            Tambah ke keranjang
                        </button>
                        <button type="button" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 transition hover:bg-white/10 hover:text-white" wire:click="remove({{ $item['id'] }})">
                            Hapus
                        </button>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</x-account.layout>
