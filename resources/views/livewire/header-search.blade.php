<div
    x-data="headerSearchStore()"
    x-on:search-committed.window="remember($event.detail.query)"
    class="relative flex-1 max-w-xl"
>
    <form
        wire:submit.prevent="submit"
        x-on:submit.prevent="remember($wire.query); $wire.submit()"
        class="flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white/70 transition focus-within:border-emerald-400/60 focus-within:text-white focus-within:ring-2 focus-within:ring-emerald-400/40"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z" />
        </svg>
        <input
            x-ref="input"
            x-on:focus="open()"
            wire:model.live.debounce.300ms="query"
            type="search"
            placeholder="Cari sneaker, brand, atau koleksi"
            class="flex-1 bg-transparent text-sm text-white placeholder:text-white/40 focus:outline-none"
        >
        <button type="submit" class="hidden"></button>
        <span wire:loading.class.remove="hidden" wire:target="query" class="hidden">
            <svg class="h-4 w-4 animate-spin text-[#4de4d4]" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v2.25a5.75 5.75 0 0 0-5.75 5.75z"></path>
            </svg>
        </span>
    </form>

    <div
        x-show="isOpen($wire.query)"
        x-transition
        x-on:keydown.escape.window="close()"
        x-on:click.away="close()"
        class="absolute top-full z-40 mt-3 w-full overflow-hidden rounded-3xl border border-white/10 bg-neutral-950/95 text-sm text-white/70 shadow-[0_24px_60px_rgba(5,15,33,0.55)] backdrop-blur-xl"
        style="display: none;"
    >
        <div class="max-h-80 overflow-y-auto">
            @if ($this->suggestions->isNotEmpty())
                <div class="border-b border-white/5 px-4 py-3 text-xs font-semibold uppercase tracking-[0.3em] text-white/40">
                    Hasil Teratas
                </div>
                <ul class="divide-y divide-white/5">
                    @foreach ($this->suggestions as $suggestion)
                        @php
                            $finalPrice = $suggestion['price'];
                            $originalPrice = null;

                            if ($suggestion['discount'] > 0) {
                                $originalPrice = (int) round($finalPrice / (1 - ($suggestion['discount'] / 100)));
                            }
                        @endphp
                        <li>
                            <a
                                href="{{ $suggestion['url'] }}"
                                class="flex items-center gap-3 px-4 py-3 transition hover:bg-white/5 hover:text-white"
                                x-on:click="remember(@js($suggestion['name'])); close()"
                            >
                                <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-2xl bg-white/10">
                                    @if ($suggestion['image'])
                                        <img src="{{ $suggestion['image'] }}" alt="{{ $suggestion['name'] }}" class="h-full w-full object-cover">
                                    @else
                                        <span class="text-xs uppercase tracking-wide text-white/40">No Image</span>
                                    @endif
                                </div>
                                <div class="flex flex-1 flex-col gap-1">
                                    <span class="font-semibold text-white">{{ $suggestion['name'] }}</span>
                                    <span class="text-xs text-white/50">⭐ {{ number_format($suggestion['rating'], 1) }} • {{ number_format((int) $suggestion['reviews']) }} ulasan</span>
                                </div>
                                <div class="text-right text-sm text-white">
                                    <span class="font-semibold">${{ number_format($finalPrice) }}</span>
                                    @if ($originalPrice)
                                        <span class="block text-xs text-white/40 line-through">${{ number_format($originalPrice) }}</span>
                                    @endif
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="px-4 py-6 text-center text-sm text-white/50">
                    Tidak ada produk yang cocok. Coba kata kunci lain.
                </div>
            @endif

            <template x-if="recent.length">
                <div class="border-t border-white/5">
                    <div class="flex items-center justify-between px-4 py-3">
                        <span class="text-xs font-semibold uppercase tracking-[0.3em] text-white/40">Pencarian Terakhir</span>
                        <button type="button" class="text-xs text-white/50 transition hover:text-white" x-on:click="clear()">Hapus</button>
                    </div>
                    <ul class="flex flex-wrap gap-2 px-4 pb-4">
                        <template x-for="item in recent" :key="item">
                            <li>
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs text-white/70 transition hover:bg-white/10"
                                    x-on:click="apply(item)"
                                    x-text="item"
                                ></button>
                            </li>
                        </template>
                    </ul>
                </div>
            </template>
        </div>
        <div class="border-t border-white/5 px-4 py-3 text-xs text-white/40">
            Tekan <kbd class="rounded border border-white/20 bg-white/5 px-1.5 py-0.5 text-[10px] text-white">Esc</kbd> untuk menutup
        </div>
    </div>
</div>
