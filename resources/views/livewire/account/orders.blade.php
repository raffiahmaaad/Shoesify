<?php

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Volt\Component;

new class extends Component {
    public array $orders = [];

    public function mount(): void
    {
        $this->loadOrders();
    }

    #[\Livewire\Attributes\On('order-updated')]
    public function loadOrders(): void
    {
        $this->orders = Auth::user()
            ->orders()
            ->with(['items.product', 'items.variant'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Order $order): array => [
                'id' => $order->id,
                'number' => $order->order_number,
                'status' => Str::headline($order->status),
                'payment_status' => Str::headline($order->payment_status),
                'total' => $order->grand_total,
                'discount' => $order->discount_total,
                'shipping' => $order->shipping_total,
                'shipping_service' => $order->shipping_service,
                'created_at' => optional($order->created_at)?->translatedFormat('d M Y, H:i'),
                'items' => $order->items->map(function ($item) {
                    $image = collect($item->product?->images ?? [])->first();

                    return [
                        'id' => $item->id,
                        'name' => $item->product?->name ?? 'Produk tidak tersedia',
                        'slug' => $item->product?->slug,
                        'variant' => trim(collect([
                            $item->variant?->color_name,
                            $item->variant?->size ? 'EU '.$item->variant->size : null,
                        ])->filter()->implode(' • ')) ?: null,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'subtotal' => $item->subtotal,
                        'image' => $image,
                    ];
                })->all(),
            ])
            ->all();
    }
}; ?>

<x-account.layout title="Riwayat Pesanan" subtitle="Pantau status pengiriman dan detail pembayaran setiap transaksi.">
    @if (empty($orders))
        <div class="glass-panel space-y-4 p-8 text-center text-white/60">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl border border-dashed border-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m4.5 5.25 1.5.375m0 0 1.125 9a2.25 2.25 0 0 0 2.238 2.001h6.873a2.25 2.25 0 0 0 2.238-2.001l.978-7.803a1.125 1.125 0 0 0-1.112-1.248H6z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m9 11.25 1.5 1.5L15 8.25" />
                </svg>
            </div>
            <div class="space-y-2">
                <h2 class="text-lg font-semibold text-white">Belum ada pesanan</h2>
                <p class="text-sm text-white/60">Mulai belanja sneaker favoritmu, dan statusnya akan muncul di sini secara otomatis.</p>
            </div>
            <div class="flex justify-center gap-3">
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-5 py-2 text-sm font-semibold text-white transition hover:bg-white/15" wire:navigate>
                    Jelajahi katalog
                    <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    @else
        <div class="space-y-6">
            @foreach ($orders as $order)
                <article class="glass-panel space-y-5 p-6" wire:key="order-card-{{ $order['id'] }}">
                    <header class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.3em] text-white/40">Nomor pesanan</p>
                            <p class="text-lg font-semibold text-white">{{ $order['number'] }}</p>
                            <p class="text-xs text-white/50">Dibuat {{ $order['created_at'] }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1 text-xs font-semibold text-white/70">
                                Status: <span class="text-white">{{ $order['status'] }}</span>
                            </span>
                            <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1 text-xs font-semibold text-white/70">
                                Pembayaran: <span class="text-white">{{ $order['payment_status'] }}</span>
                            </span>
                            @if ($order['shipping_service'])
                                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1 text-xs text-white/70">
                                    Pengiriman: <span class="text-white">{{ $order['shipping_service'] }}</span>
                                </span>
                            @endif
                        </div>
                    </header>

                    <div class="divide-y divide-white/10 rounded-3xl border border-white/10 bg-white/[0.04]">
                        @foreach ($order['items'] as $item)
                            <div class="flex flex-col gap-4 px-4 py-4 md:flex-row md:items-center md:justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="h-16 w-16 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
                                        @if ($item['image'])
                                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover">
                                        @endif
                                    </div>
                                    <div class="flex flex-col gap-1 text-sm text-white/70">
                                        <span class="font-semibold text-white">{{ $item['name'] }}</span>
                                        @if ($item['variant'])
                                            <span class="text-xs text-white/50">{{ $item['variant'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-6 text-sm text-white/70">
                                    <span>x{{ $item['quantity'] }}</span>
                                    <div class="text-right">
                                        <p class="font-semibold text-white">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                                        <p class="text-xs text-white/50">Rp {{ number_format($item['price'], 0, ',', '.') }} / item</p>
                                    </div>
                                    @if ($item['slug'])
                                        <a href="{{ route('products.show', $item['slug']) }}" class="text-xs text-[#4de4d4] transition hover:text-white" wire:navigate>
                                            Lihat produk
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <footer class="flex flex-wrap items-center justify-between gap-3 text-sm text-white/70">
                        <div class="flex flex-col gap-1">
                            <span>Diskon: Rp {{ number_format($order['discount'], 0, ',', '.') }}</span>
                            <span>Ongkir: Rp {{ number_format($order['shipping'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs uppercase tracking-[0.3em] text-white/40">Total</span>
                            <span class="text-xl font-semibold text-white">Rp {{ number_format($order['total'], 0, ',', '.') }}</span>
                        </div>
                        <a
                            href="{{ route('account.orders.show', $order['id']) }}"
                            class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-5 py-2 text-xs font-semibold text-white transition hover:bg-white/15"
                            wire:navigate
                        >
                            Detail pesanan
                            <span aria-hidden="true">→</span>
                        </a>
                    </footer>
                </article>
            @endforeach
        </div>
    @endif
</x-account.layout>
