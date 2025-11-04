<?php

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public Order $order;

    public function mount(Order $order): void
    {
        abort_unless($order->user_id === Auth::id(), 403);
        $this->order->load(['items.product.brand', 'items.variant']);
    }
}; ?>

<x-account.layout title="Detail Pesanan #{{ $order->order_number }}" subtitle="Ringkasan lengkap status pembayaran dan pengiriman.">
    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <div class="space-y-6">
            <section class="glass-panel space-y-4 p-6">
                <header class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-white/40">Status pemesanan</p>
                        <p class="text-lg font-semibold text-white">{{ \Illuminate\Support\Str::headline($order->status) }}</p>
                        <p class="text-xs text-white/50">Dibuat pada {{ optional($order->created_at)?->translatedFormat('d F Y, H:i') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2 text-xs text-white/70">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1">Pembayaran: <span class="text-white">{{ \Illuminate\Support\Str::headline($order->payment_status) }}</span></span>
                        @if ($order->shipping_service)
                            <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1">Pengiriman: <span class="text-white">{{ $order->shipping_service }}</span></span>
                        @endif
                    </div>
                </header>
                <div class="rounded-3xl border border-white/10 bg-white/[0.04]">
                    @foreach ($order->items as $item)
                        <div class="flex flex-col gap-4 px-4 py-4 md:flex-row md:items-center md:justify-between">
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
                                    @if ($image = collect($item->product?->images ?? [])->first())
                                        <img src="{{ $image }}" alt="{{ $item->product?->name }}" class="h-full w-full object-cover">
                                    @endif
                                </div>
                                <div class="flex flex-col gap-1 text-sm text-white/70">
                                    <span class="font-semibold text-white">{{ $item->product?->name ?? 'Produk tidak tersedia' }}</span>
                                    @if ($variant = trim(collect([$item->variant?->color_name, $item->variant?->size ? 'EU '.$item->variant->size : null])->filter()->implode(' • ')))
                                        <span class="text-xs text-white/50">{{ $variant }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-6 text-sm text-white/70">
                                <span>x{{ $item->quantity }}</span>
                                <div class="text-right">
                                    <p class="font-semibold text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                    <p class="text-xs text-white/50">Rp {{ number_format($item->price, 0, ',', '.') }} / item</p>
                                </div>
                                @if ($item->product?->slug)
                                    <a href="{{ route('products.show', $item->product->slug) }}" class="text-xs text-[#4de4d4] transition hover:text-white" wire:navigate>
                                        Lihat produk
                                    </a>
                                @endif
                            </div>
                        </div>
                        @if (! $loop->last)
                            <div class="border-t border-white/10"></div>
                        @endif
                    @endforeach
                </div>
            </section>

            <section class="glass-panel space-y-4 p-6">
                <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-white/40">Alamat pengiriman</h3>
                @php($shipping = $order->shipping_address ?? [])
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-white/70">
                    <p class="text-white font-semibold">{{ $shipping['recipient_name'] ?? '-' }}</p>
                    <p class="text-xs text-white/50">{{ $shipping['phone'] ?? '-' }}</p>
                    <p class="mt-1 text-xs leading-5 text-white/60">
                        {{ $shipping['address_line1'] ?? '' }} {{ $shipping['address_line2'] ?? '' }}<br>
                        {{ $shipping['city'] ?? '' }}, {{ $shipping['province'] ?? '' }}<br>
                        {{ $shipping['postal_code'] ?? '' }} {{ $shipping['country'] ?? '' }}
                    </p>
                </div>
                @if (! empty($order->notes))
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-white/60">
                        <p class="text-xs uppercase tracking-[0.3em] text-white/40 mb-1">Catatan</p>
                        {{ $order->notes }}
                    </div>
                @endif
            </section>
        </div>

        <aside class="space-y-6">
            <section class="glass-panel space-y-4 p-6">
                <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-white/40">Ringkasan pembayaran</h3>
                <dl class="space-y-3 text-sm text-white/70">
                    <div class="flex items-center justify-between">
                        <dt>Subtotal</dt>
                        <dd class="text-white font-semibold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt>Diskon</dt>
                        <dd class="text-emerald-300 font-semibold">- Rp {{ number_format($order->discount_total, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt>Ongkos kirim</dt>
                        <dd class="text-white font-semibold">Rp {{ number_format($order->shipping_total, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt>Pajak</dt>
                        <dd class="text-white font-semibold">Rp {{ number_format($order->tax_total, 0, ',', '.') }}</dd>
                    </div>
                </dl>
                <div class="flex items-center justify-between rounded-2xl border border-white/15 bg-white/5 px-4 py-3 text-white">
                    <span class="text-sm text-white/60">Total dibayar</span>
                    <span class="text-xl font-semibold">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                </div>
                @if ($order->payment_method === 'midtrans' && $order->payment_status === 'pending')
                    <p class="text-xs text-white/60">
                        Pembayaran belum selesai. Gunakan kembali token Snap dari email konfirmasi untuk melanjutkan transaksi di Midtrans.
                    </p>
                @elseif ($order->payment_method === 'bank_transfer' && $order->payment_status === 'pending')
                    <p class="text-xs text-white/60">
                        Transfer manual biasanya diverifikasi dalam 1x24 jam kerja setelah bukti transfer dikirim.
                    </p>
                @endif
            </section>

            <section class="glass-panel space-y-3 p-6 text-sm text-white/70">
                <h3 class="text-xs uppercase tracking-[0.3em] text-white/40">Bantuan</h3>
                <p>Perlu bantuan? Hubungi concierge Shoesify via WhatsApp atau email support@shoesify.id.</p>
                <div class="flex flex-wrap gap-3 text-xs">
                    <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-white transition hover:bg-white/15">
                        WhatsApp
                    </a>
                    <a href="mailto:support@shoesify.id" class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-white transition hover:bg-white/15">
                        Kirim Email
                    </a>
                </div>
            </section>
        </aside>
    </div>
</x-account.layout>
