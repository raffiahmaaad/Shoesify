<div
    x-data="{ open: false }"
    x-on:mouseenter="open = true"
    x-on:mouseleave="open = false"
    class="relative"
>
    <a
        href="#"
        class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white transition hover:bg-white/15"
        aria-label="Buka keranjang"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m3 3 1.88 1.88A2 2 0 0 0 6.285 6H19.5a1 1 0 0 1 .969 1.243l-1.5 6A1 1 0 0 1 18 14H7.165a2 2 0 0 1-1.948-1.561L3.28 4.879M12 19a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" />
        </svg>
    </a>
    @if ($count > 0)
        <span class="absolute -right-0.5 -top-0.5 inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-[#f57b51] px-1 text-[11px] font-semibold text-white shadow-[0_10px_24px_rgba(245,123,81,0.45)]">
            {{ $count }}
        </span>
    @endif
    <div
        x-show="open"
        x-transition
        class="absolute right-0 z-50 mt-4 w-72 rounded-3xl border border-white/10 bg-neutral-950/95 p-5 text-sm text-white/60 shadow-[0_24px_60px_rgba(5,15,33,0.55)] backdrop-blur-xl"
        style="display: none;"
    >
        @if ($count > 0)
            <p class="text-sm text-white">Ada {{ $count }} item di keranjangmu.</p>
            <a href="#" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-full bg-[#4de4d4] px-4 py-2 text-sm font-semibold text-neutral-900 transition hover:-translate-y-0.5">
                Buka Keranjang
                <span aria-hidden="true">→</span>
            </a>
        @else
            <div class="space-y-3 text-center">
                <p class="font-semibold text-white">Keranjang masih kosong</p>
                <p class="text-xs text-white/50">Jelajahi koleksi kami dan tambahkan sneaker favoritmu.</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold text-white transition hover:bg-white/10">
                    Mulai belanja
                </a>
            </div>
        @endif
    </div>
</div>
