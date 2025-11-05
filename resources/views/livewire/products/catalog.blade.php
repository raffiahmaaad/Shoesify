<div class="grid gap-10 lg:grid-cols-[320px_1fr]">
    <aside class="space-y-6 lg:sticky lg:top-28">
        <div class="glass-panel space-y-6 p-6">
            <div class="space-y-2">
                <label for="product-search" class="text-xs font-semibold uppercase tracking-[0.3em] text-white/50">Cari sneaker</label>
                <div class="flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white/70 focus-within:border-[#4de4d4]/60 focus-within:text-white focus-within:ring-2 focus-within:ring-[#4de4d4]/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z" />
                    </svg>
                    <input id="product-search" type="search" wire:model.live.debounce.400ms="search" placeholder="Cari model, brand, atau warna" class="w-full bg-transparent text-sm placeholder:text-white/40 focus:outline-none">
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-white/50">Harga</h3>
                    <span class="text-xs text-white/60">Rp {{ number_format($priceMin, 0, ',', '.') }} – Rp {{ number_format($priceMax, 0, ',', '.') }}</span>
                </div>
                <div class="space-y-3">
                    <input type="range" min="{{ $priceBounds['min'] }}" max="{{ $priceBounds['max'] }}" step="1" wire:model.live="priceMin" class="w-full accent-[#4de4d4]">
                    <input type="range" min="{{ $priceBounds['min'] }}" max="{{ $priceBounds['max'] }}" step="1" wire:model.live="priceMax" class="w-full accent-[#4de4d4]">
                </div>
            </div>

            <div class="space-y-3">
                <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-white/50">Brand</h3>
                <div class="grid gap-2">
                    @foreach ($brands as $brand)
                        @php $brandId = 'brand-'.\Illuminate\Support\Str::slug($brand); @endphp
                        <label for="{{ $brandId }}" class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-2 text-sm text-white/70 transition hover:bg-white/[0.08]">
                            <span>{{ $brand }}</span>
                            <input id="{{ $brandId }}" type="checkbox" value="{{ $brand }}" wire:model.live="selectedBrands" class="h-4 w-4 accent-[#4de4d4]">
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="space-y-3">
                <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-white/50">Ukuran</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($sizes as $size)
                        @php $active = in_array($size, $selectedSizes, true); @endphp
                        <label class="inline-flex cursor-pointer items-center justify-center rounded-full border border-white/15 px-3 py-1.5 text-sm {{ $active ? 'bg-white text-neutral-900' : 'bg-white/5 text-white/70 hover:bg-white/10' }} transition">
                            <input type="checkbox" value="{{ $size }}" wire:model.live="selectedSizes" class="hidden">
                            {{ $size }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="space-y-3">
                <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-white/50">Warna</h3>
                <div class="flex flex-wrap gap-3">
                    @foreach ($colorPalette as $color)
                        @php $active = in_array($color['name'], $selectedColors, true); @endphp
                        <label class="group relative flex cursor-pointer flex-col items-center gap-2 text-xs">
                            <input type="checkbox" value="{{ $color['name'] }}" wire:model.live="selectedColors" class="hidden">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full border-2 {{ $active ? 'border-white' : 'border-white/20 group-hover:border-white/60' }} transition">
                                <span class="h-8 w-8 rounded-full border border-white/20 shadow-[0_6px_18px_rgba(0,0,0,0.35)]" style="background-color: {{ $color['hex'] }}"></span>
                            </span>
                            <span class="text-white/60">{{ $color['name'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <button type="button" wire:click="resetFilters" class="flex w-full items-center justify-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6h18M6 6v13m12-13v13M9 19h6" />
            </svg>
            Reset semua filter
        </button>
    </aside>

    <div class="space-y-8">
        <div class="flex flex-col gap-4 rounded-[28px] border border-white/10 bg-white/[0.04] p-6 md:flex-row md:items-center md:justify-between">
            <div class="space-y-1">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/50">Hasil</p>
                <h2 class="text-xl font-semibold text-white">
                    Menampilkan {{ $resultsCount }} produk premium
                </h2>
                @if ($search !== '')
                    <p class="text-sm text-white/60">
                        Filter kata kunci: <span class="font-medium text-[#4de4d4]">“{{ $search }}”</span>
                    </p>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <label for="catalog-sort" class="text-xs font-semibold uppercase tracking-[0.3em] text-white/50">Urutkan</label>
                <select id="catalog-sort" wire:model.live="sort" class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm text-white/80 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/40">
                    @foreach ($sortOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <div class="flex items-center rounded-full border border-white/15 bg-white/5 p-1">
                    <button type="button" wire:click="toggleView('grid')" class="inline-flex items-center justify-center rounded-full px-3 py-1.5 text-sm {{ $view === 'grid' ? 'bg-[#4de4d4] text-neutral-900' : 'text-white/60 hover:text-white' }} transition">
                        Grid
                    </button>
                    <button type="button" wire:click="toggleView('list')" class="inline-flex items-center justify-center rounded-full px-3 py-1.5 text-sm {{ $view === 'list' ? 'bg-[#4de4d4] text-neutral-900' : 'text-white/60 hover:text-white' }} transition">
                        List
                    </button>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            @if (empty($visibleProducts))
                <div class="glass-panel flex flex-col items-center gap-4 p-12 text-center text-white/60">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="m8 16 3-3-3-3m5 6 3-3-3-3M3 5v5a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5M5 21h14" />
                    </svg>
                    <div class="space-y-2">
                        <h3 class="text-lg font-semibold text-white">Belum ada produk yang sesuai</h3>
                        <p class="text-sm">Coba ubah filter pencarian atau reset untuk melihat koleksi lengkap kami.</p>
                    </div>
                    <button type="button" wire:click="resetFilters" class="rounded-full border border-white/15 bg-white/10 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/15">
                        Clear filters
                    </button>
                </div>
            @else
                @if ($view === 'grid')
                    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($visibleProducts as $product)
                            <article wire:key="product-grid-{{ $product['id'] }}" class="hover-card group relative flex flex-col overflow-hidden rounded-[28px] border border-white/10 bg-white/[0.05] p-6 text-white/70" data-product-card data-product='@json($product)'>
                                <div class="relative mb-5 h-52 overflow-hidden rounded-3xl">
                                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                    @if ($product['discount'] > 0)
                                        <span class="absolute left-4 top-4 rounded-full bg-[#f57b51] px-3 py-1 text-xs font-semibold text-white shadow-lg">-{{ $product['discount'] }}%</span>
                                    @endif
                                    <button type="button" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/10 text-white transition hover:bg-white/20" data-wishlist>
                                        <span class="sr-only">Tambah ke wishlist</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 8.25-8.485 8.485a2.121 2.121 0 0 1-3 0L3 8.25" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 6.75c-1.5-1.5-3.75-1.5-5.25 0L12 9l-2.25-2.25c-1.5-1.5-3.75-1.5-5.25 0s-1.5 3.75 0 5.25L12 21l7.5-9a3.708 3.708 0 0 0 0-5.25z" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex flex-1 flex-col gap-4">
                                    <div class="space-y-2 text-white/70">
                                        <div class="flex items-center justify-between text-xs uppercase tracking-[0.3em] text-white/50">
                                            <span class="font-medium text-[#4de4d4]">{{ $product['brand'] }}</span>
                                            <span class="inline-flex items-center gap-1 rounded-full border border-white/15 bg-white/10 px-2.5 py-1 text-[11px] font-semibold text-white/70 dark:border-zinc-700 dark:bg-zinc-800/70 dark:text-zinc-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7.5h18m-2.25-3v15a1.5 1.5 0 0 1-1.5 1.5h-10.5a1.5 1.5 0 0 1-1.5-1.5v-15" />
                                                </svg>
                                                {{ $product['in_stock'] ? 'Stok ' . $product['stock'] : 'Stok habis' }}
                                            </span>
                                        </div>
                                        <h3 class="text-lg font-semibold text-white">{{ $product['name'] }}</h3>
                                        @if ($product['category_name'])
                                            <p class="text-xs text-white/50">Kategori: {{ $product['category_name'] }}</p>
                                        @endif
                                        <p class="text-sm text-white/60">{{ $product['description'] }}</p>
                                    </div>
                                    <div class="flex items-center gap-1 text-sm text-[#f8c572]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                            <path d="m12 2.5 2.263 6.483h6.612l-5.352 3.891 2.263 6.483L12 15.466l-5.786 3.791 2.263-6.483-5.352-3.891h6.612L12 2.5z" />
                                        </svg>
                                        <span>{{ number_format($product['rating'], 1) }}</span>
                                        <span class="text-white/40">({{ number_format($product['reviews']) }})</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @foreach ($product['colors'] as $color)
                                            <span class="h-4 w-4 rounded-full border border-white/20" style="background-color: {{ $color['hex'] }}"></span>
                                        @endforeach
                                    </div>
                                    <div class="mt-auto flex items-center justify-between">
                                        <div class="flex items-baseline gap-2 text-white">
                                            <span class="text-xl font-semibold">Rp {{ number_format($product['price'], 0, ',', '.') }}</span>
                                            @if ($product['discount'] > 0)
                                                <span class="text-sm text-white/40 line-through">
                                                    Rp {{ number_format($product['price'] / (1 - $product['discount'] / 100), 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" class="hidden items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20 group-hover:flex" data-quick-view="{{ $product['id'] }}">
                                                Quick view
                                            </button>
                                            <button type="button" class="rounded-full bg-[#016b61] px-4 py-2 text-sm font-semibold text-white shadow-[0_20px_45px_rgba(1,107,97,0.45)] transition hover:-translate-y-0.5" data-add-to-cart>
                                                Add to cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($visibleProducts as $product)
                            <article wire:key="product-list-{{ $product['id'] }}" class="hover-card relative grid gap-6 rounded-[28px] border border-white/10 bg-white/[0.04] p-6 text-white/70 md:grid-cols-[0.9fr_1fr]" data-product-card data-product='@json($product)'>
                                <div class="relative h-56 overflow-hidden rounded-3xl">
                                    @if ($product['image'])
                                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="h-full w-full object-cover transition duration-500 dark:brightness-90">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-white/5 text-xs uppercase tracking-[0.3em] text-white/50 dark:bg-zinc-800/70 dark:text-zinc-400">
                                            No Image
                                        </div>
                                    @endif
                                    @if ($product['discount'] > 0)
                                        <span class="absolute left-4 top-4 rounded-full bg-[#f57b51] px-3 py-1 text-xs font-semibold text-white shadow-lg">-{{ $product['discount'] }}%</span>
                                    @endif
                                </div>
                                <div class="flex flex-col gap-4">
                                    <div class="flex items-center justify-between">
                                        <div class="space-y-2 text-white/70">
                                            <div class="flex items-center gap-3 text-xs uppercase tracking-[0.3em] text-white/50">
                                                <span class="font-medium text-[#4de4d4]">{{ $product['brand'] }}</span>
                                                <span class="inline-flex items-center gap-1 rounded-full border border-white/15 bg-white/10 px-2.5 py-1 text-[11px] font-semibold text-white/70 dark:border-zinc-700 dark:bg-zinc-800/70 dark:text-zinc-200">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7.5h18m-2.25-3v15a1.5 1.5 0 0 1-1.5 1.5h-10.5a1.5 1.5 0 0 1-1.5-1.5v-15" />
                                                    </svg>
                                                    {{ $product['in_stock'] ? 'Stok ' . $product['stock'] : 'Stok habis' }}
                                                </span>
                                            </div>
                                            <h3 class="text-2xl font-semibold text-white">{{ $product['name'] }}</h3>
                                            @if ($product['category_name'])
                                                <p class="text-xs text-white/50">Kategori: {{ $product['category_name'] }}</p>
                                            @endif
                                        </div>
                                        <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/10 text-white transition hover:bg-white/20" data-wishlist>
                                            <span class="sr-only">Tambah ke wishlist</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 8.25-8.485 8.485a2.121 2.121 0 0 1-3 0L3 8.25" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 6.75c-1.5-1.5-3.75-1.5-5.25 0L12 9l-2.25-2.25c-1.5-1.5-3.75-1.5-5.25 0s-1.5 3.75 0 5.25L12 21l7.5-9a3.708 3.708 0 0 0 0-5.25z" />
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="text-sm text-white/60 md:text-base">{{ $product['description'] }}</p>
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-[#f8c572]">
                                        <div class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                                <path d="m12 2.5 2.263 6.483h6.612l-5.352 3.891 2.263 6.483L12 15.466l-5.786 3.791 2.263-6.483-5.352-3.891h6.612L12 2.5z" />
                                            </svg>
                                            <span>{{ number_format($product['rating'], 1) }}</span>
                                        </div>
                                        <span class="text-white/40">{{ number_format($product['reviews']) }} ulasan</span>
                                        <span class="text-white/50">|</span>
                                        <span class="text-white/60">Ukuran: {{ implode(', ', $product['sizes']) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-baseline gap-2 text-white">
                                            <span class="text-2xl font-semibold">Rp {{ number_format($product['price'], 0, ',', '.') }}</span>
                                            @if ($product['discount'] > 0)
                                                <span class="text-sm text-white/40 line-through">
                                                    Rp {{ number_format($product['price'] / (1 - $product['discount'] / 100), 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button type="button" class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20" data-quick-view="{{ $product['id'] }}">
                                                Quick view
                                            </button>
                                            <button type="button" class="rounded-full bg-[#016b61] px-5 py-2 text-sm font-semibold text-white shadow-[0_20px_45px_rgba(1,107,97,0.45)] transition hover:-translate-y-0.5" data-add-to-cart>
                                                Add to cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>

        @if ($hasMore)
            <div class="flex justify-center">
                <button type="button" wire:click="loadMore" wire:loading.attr="disabled" wire:target="loadMore" class="rounded-full border border-white/15 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="loadMore">Load more</span>
                    <span wire:loading wire:target="loadMore" class="flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin text-[#4de4d4]" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v2.5a5.5 5.5 0 0 0-5.5 5.5z"></path>
                        </svg>
                        Loading
                    </span>
                </button>
            </div>
        @endif
    </div>
</div>
