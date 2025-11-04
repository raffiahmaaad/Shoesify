@php
    $previewItems ??= collect();
    $hasItems = $previewItems->isNotEmpty();
@endphp

<div
    x-data="{ open: false }"
    x-on:mouseenter="open = true"
    x-on:mouseleave="open = false"
    x-on:keydown.escape.window="open = false"
    class="relative"
>
    <button
        type="button"
        class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white transition hover:bg-white/15 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-neutral-950"
        aria-label="Buka keranjang"
        x-on:click="open = !open"
        x-bind:aria-expanded="open"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m3 3 1.88 1.88A2 2 0 0 0 6.285 6H19.5a1 1 0 0 1 .969 1.243l-1.5 6A1 1 0 0 1 18 14H7.165a2 2 0 0 1-1.948-1.561L3.28 4.879M12 19a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" />
        </svg>
    </button>
    @if ($count > 0)
        <span class="absolute -right-0.5 -top-0.5 inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-[#f57b51] px-1 text-[11px] font-semibold text-white shadow-[0_10px_24px_rgba(245,123,81,0.45)]">
            {{ $count }}
        </span>
    @endif
    <div
        x-cloak
        x-show="open"
        x-transition
        class="glass-dropdown absolute right-0 z-50 mt-4 w-80 p-4 text-sm"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-3">
            <div>
                <p class="text-[11px] uppercase tracking-[0.35em] text-white/40">Keranjang</p>
                <p class="text-base font-semibold text-white">Preview order</p>
            </div>
            <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-white/90">
                {{ $count }} item{{ $count === 1 ? '' : 's' }}
            </span>
        </div>

        @if ($hasItems)
            <div class="custom-scroll max-h-72 space-y-3 overflow-y-auto py-3 pr-1">
                @foreach ($previewItems as $item)
                    @php
                        $product = $item->product;
                        $cover = data_get($product?->images, 0);
                        $size = $item->variant?->size ?? data_get($item->metadata, 'size');
                        $color = $item->variant?->color_name ?? data_get($item->metadata, 'color_name') ?? data_get($item->metadata, 'color');
                        $lineTotal = (int) ($item->line_total ?? ($item->quantity * (int) ($item->unit_price ?? 0)));
                    @endphp
                    <article class="grid grid-cols-[56px_1fr] items-center gap-3 rounded-2xl border border-white/5 bg-white/5 p-3">
                        <div class="h-14 w-14 overflow-hidden rounded-2xl bg-white/10">
                            @if ($cover)
                                <img src="{{ $cover }}" alt="{{ $product?->name }}" class="h-full w-full object-cover" loading="lazy">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-[11px] text-white/40">
                                    No Image
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col text-white">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-semibold leading-tight">{{ $product?->name ?? 'Produk tidak tersedia' }}</p>
                                <span class="text-xs text-white/60">×{{ $item->quantity }}</span>
                            </div>
                            @if ($size || $color)
                                <p class="text-[11px] text-white/55">
                                    {{ $size ? 'EU ' . $size : '' }}{{ $size && $color ? ' • ' : '' }}{{ $color }}
                                </p>
                            @endif
                            <p class="mt-2 text-sm font-semibold text-[#4de4d4]">${{ number_format($lineTotal, 0) }}</p>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="space-y-3 rounded-2xl bg-white/5 p-4 text-white/85">
                <div class="flex items-center justify-between text-sm">
                    <span>Subtotal</span>
                    <span class="text-base font-semibold text-white">${{ number_format(max($previewTotal, 0), 0) }}</span>
                </div>
                <a href="#" class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-[#4de4d4] px-4 py-2 text-sm font-semibold text-neutral-900 transition hover:-translate-y-0.5">
                    Buka Keranjang
                    <span aria-hidden="true">→</span>
                </a>
            </div>
        @else
            <div class="space-y-4 py-6 text-center text-white">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl border border-dashed border-white/25 bg-white/5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3H6.75A2.25 2.25 0 0 0 4.5 5.25v13.5A2.25 2.25 0 0 0 6.75 21h6.75a2.25 2.25 0 0 0 2.25-2.25V15M12 15l3-3m0 0-3-3m3 3H3" />
                    </svg>
                </div>
                <div class="space-y-1 text-sm">
                    <p class="font-semibold">Keranjang masih kosong</p>
                    <p class="text-white/60">Mulai jelajahi katalog futuristik kami dan tambahkan sneaker favoritmu.</p>
                </div>
                <a href="{{ route('products.index') }}#katalog" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs font-semibold text-white transition hover:bg-white/10">
                    Lihat Katalog
                </a>
            </div>
        @endif
    </div>
</div>
