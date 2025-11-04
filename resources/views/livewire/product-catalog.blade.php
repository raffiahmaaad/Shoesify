@php
    $displayedProducts = $this->filteredProducts->take($perPage);
@endphp

<section class="mx-auto w-full max-w-7xl space-y-10 px-4 pb-24 pt-10 md:px-10">
    <nav class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.25em] text-white/40">
        <a href="{{ route('home') }}" class="transition hover:text-white/70">Home</a>
        <span>/</span>
        <span class="text-white/70">Shop</span>
    </nav>

    <header class="glass-panel flex flex-col gap-6 overflow-hidden border-white/10 bg-white/[0.05] px-6 py-8 md:flex-row md:items-center md:justify-between">
        <div class="space-y-3">
            <h1 class="text-3xl font-semibold text-white md:text-4xl">Shoesify Marketplace</h1>
            <p class="max-w-2xl text-sm text-white/60 md:text-base">
                Filter real-time collections by brand, fit, and color. Every product is NFC-verified and curated for premium performance.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3 text-sm text-white/60">
            <div class="rounded-full border border-white/10 bg-white/5 px-4 py-2">
                {{ $this->filteredProducts->count() }} items curated
            </div>
            <div class="flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2">
                <span class="flex h-2.5 w-2.5 items-center justify-center">
                    <span class="h-2 w-2 rounded-full bg-[#4de4d4]"></span>
                </span>
                Live inventory synced
            </div>
        </div>
    </header>

    <div class="grid gap-8 lg:grid-cols-[320px_1fr]">
        <aside class="glass-panel h-max space-y-8 border-white/10 bg-white/[0.04] p-6" wire:key="filters">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-white">Refine results</h2>
                <button type="button" wire:click="resetFilters" class="text-xs font-medium text-white/50 transition hover:text-white/80">
                    Reset
                </button>
            </div>

            <div class="space-y-6">
                <div class="space-y-3">
                    <label for="catalog-search" class="text-xs uppercase tracking-[0.3em] text-white/40">Search</label>
                    <div class="relative">
                        <input id="catalog-search" type="search" placeholder="Search sneaker, brand, or terrain" wire:model.live.debounce.400ms="search" class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-white/30 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/70" />
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 21-4.35-4.35M11 18.5a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z" />
                        </svg>
                    </div>
                </div>

                <div class="space-y-3">
                    <p class="text-xs uppercase tracking-[0.3em] text-white/40">Price Range</p>
                    <div class="space-y-3 rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="flex items-center justify-between text-sm text-white/60">
                            <span>${{ $priceMin }}</span>
                            <span>${{ $priceMax }}</span>
                        </div>
                        <div class="flex flex-col gap-3">
                            <label class="text-xs text-white/40" for="price-min">Minimum</label>
                            <input id="price-min" type="range" min="100" max="300" step="1" wire:model.live="priceMin" class="accent-[#4de4d4]" />
                            <label class="text-xs text-white/40" for="price-max">Maximum</label>
                            <input id="price-max" type="range" min="120" max="350" step="1" wire:model.live="priceMax" class="accent-[#7d5eff]" />
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <p class="text-xs uppercase tracking-[0.3em] text-white/40">Brand</p>
                    <div class="grid gap-2">
                        @foreach ($this->brands as $brand)
                            @php
                                $brandId = 'brand-' . \Illuminate\Support\Str::slug($brand);
                            @endphp
                            <label for="{{ $brandId }}" class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white/70 transition hover:border-white/30 hover:text-white">
                                <div class="flex items-center gap-3">
                                    <input id="{{ $brandId }}" type="checkbox" value="{{ $brand }}" wire:model.live="selectedBrands" class="h-4 w-4 rounded border-white/20 bg-white/10 text-[#4de4d4] focus:ring-emerald-400" />
                                    <span>{{ $brand }}</span>
                                </div>
                                @php
                                    $count = $this->filteredProducts->filter(fn ($product) => $product['brand'] === $brand)->count();
                                @endphp
                                <span class="text-xs text-white/40">{{ $count }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-3">
                    <p class="text-xs uppercase tracking-[0.3em] text-white/40">Size</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($this->sizes as $size)
                            <label class="cursor-pointer rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs font-medium text-white/70 transition hover:border-white/40 hover:text-white">
                                <input type="checkbox" value="{{ $size }}" wire:model.live="selectedSizes" class="peer sr-only" />
                                <span class="peer-checked:text-[#4de4d4]">{{ $size }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-3">
                    <p class="text-xs uppercase tracking-[0.3em] text-white/40">Color</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($this->colors as $color)
                            <label class="flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-2 text-xs text-white/70 transition hover:border-white/30 hover:text-white">
                                <input type="checkbox" value="{{ $color }}" wire:model.live="selectedColors" class="peer sr-only" />
                                <span class="h-3 w-3 rounded-full border border-white/10" style="background: {{ match ($color) {
                                    'Teal' => '#4de4d4',
                                    'Black' => '#000',
                                    'Yellow' => '#fbbf24',
                                    'Charcoal' => '#111827',
                                    'Sky' => '#bae6fd',
                                    'Obsidian' => '#1e293b',
                                    'White' => '#f8fafc',
                                    'Midnight' => '#0f172a',
                                    'Olive' => '#14532d',
                                    'Stone' => '#e2e8f0',
                                    'Infrared' => '#f87171',
                                    'Cream' => '#f5f5f4',
                                    'Saffron' => '#f97316',
                                    'Graphite' => '#1f2937',
                                    'Lime' => '#84cc16',
                                    'Lilac' => '#c4b5fd',
                                    'Onyx' => '#111827',
                                    'Amber' => '#f59e0b',
                                    'Ice' => '#e0f2fe',
                                    'Crimson' => '#dc2626',
                                    'Slate' => '#334155',
                                    default => '#64748b',
                                } }}"></span>
                                <span class="peer-checked:text-[#4de4d4]">{{ $color }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>

        <section class="space-y-6">
            <div class="flex flex-col gap-4 rounded-2xl border border-white/10 bg-white/[0.05] p-4 md:flex-row md:items-center md:justify-between">
                <div class="flex flex-wrap items-center gap-3 text-xs uppercase tracking-[0.3em] text-white/40">
                    <span>Sort by</span>
                    <select wire:model.live="sort" class="rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/70">
                        <option value="popular">Popular</option>
                        <option value="price_asc">Price: Low to High</option>
                        <option value="price_desc">Price: High to Low</option>
                        <option value="newest">Newest</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="setView('grid')" class="flex h-10 w-10 items-center justify-center rounded-full border {{ $view === 'grid' ? 'border-[#4de4d4]/60 bg-[#4de4d4]/20 text-[#4de4d4]' : 'border-white/15 bg-white/5 text-white/70' }} transition hover:border-white/40 hover:text-white">
                        <span class="sr-only">Grid view</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h4v4H4V6zm6 0h4v4h-4V6zm6 0h4v4h-4V6zM4 12h4v4H4v-4zm6 0h4v4h-4v-4zm6 0h4v4h-4v-4z" />
                        </svg>
                    </button>
                    <button type="button" wire:click="setView('list')" class="flex h-10 w-10 items-center justify-center rounded-full border {{ $view === 'list' ? 'border-[#4de4d4]/60 bg-[#4de4d4]/20 text-[#4de4d4]' : 'border-white/15 bg-white/5 text-white/70' }} transition hover:border-white/40 hover:text-white">
                        <span class="sr-only">List view</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <div wire:loading.flex class="glass-panel fixed inset-0 z-40 hidden items-center justify-center bg-neutral-950/70">
                <div class="flex flex-col items-center gap-3 text-sm text-white/70">
                    <svg class="h-8 w-8 animate-spin text-[#4de4d4]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" role="img" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4.5l3.5-3.5A8 8 0 0 1 20 12h-4.5l3.5 3.5A8 8 0 0 1 12 20v-4.5l-3.5 3.5A8 8 0 0 1 4 12z"></path>
                    </svg>
                    <p>Updating catalog…</p>
                </div>
            </div>

            @if ($displayedProducts->isEmpty())
                <div class="glass-panel flex flex-col items-center gap-4 border-white/10 bg-white/[0.04] px-6 py-16 text-center text-white/60">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M11 18.5a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z" />
                    </svg>
                    <div class="space-y-2">
                        <p class="text-lg font-semibold text-white">No sneakers match these filters</p>
                        <p class="text-sm text-white/50">Try adjusting size, price range, or clear filters to explore more drops.</p>
                    </div>
                    <button type="button" wire:click="resetFilters" class="rounded-full border border-white/15 bg-white/10 px-6 py-3 text-sm font-semibold text-white transition hover:border-white/40 hover:bg-white/15">
                        Clear filters
                    </button>
                </div>
            @else
                <div class="space-y-6">
                    @if ($view === 'grid')
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($displayedProducts as $product)
                                <article class="hover-card flex flex-col overflow-hidden rounded-[28px] border border-white/10 bg-white/[0.05] shadow-[0_30px_60px_rgba(5,15,33,0.35)] transition duration-300">
                                    <div class="relative h-56 overflow-hidden">
                                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="h-full w-full object-cover transition duration-500 hover:scale-105" loading="lazy">
                                        <div class="absolute left-4 top-4 flex flex-col gap-2">
                                            <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-medium text-white">
                                                {{ $product['badge'] }}
                                            </span>
                                            @if ($product['discount'])
                                                <span class="inline-flex items-center gap-2 rounded-full bg-[#f57b51] px-3 py-1 text-xs font-semibold shadow-lg">
                                                    -{{ $product['discount'] }}%
                                                </span>
                                            @endif
                                        </div>
                                        <div class="absolute right-4 top-4 flex flex-col gap-2 text-xs text-white/60">
                                            <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1 uppercase tracking-[0.3em]">
                                                {{ $product['terrain'] }}
                                            </span>
                                            <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1">
                                                {{ $product['in_stock'] ? 'In stock' : 'Notify me' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex flex-1 flex-col gap-4 p-6 text-white/70">
                                        <div class="flex items-center justify-between text-xs text-white/40">
                                            <span>{{ $product['brand'] }}</span>
                                            <span class="flex items-center gap-1 text-[#f8c572]">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                                    <path d="m12 2.5 2.263 6.483h6.612l-5.352 3.891 2.263 6.483L12 15.466l-5.786 3.791 2.263-6.483-5.352-3.891h6.612L12 2.5z" />
                                                </svg>
                                                {{ number_format($product['rating'], 1) }}
                                                <span class="text-xs text-white/40">({{ number_format($product['reviews']) }})</span>
                                            </span>
                                        </div>
                                        <div class="space-y-2 text-white">
                                            <h3 class="text-lg font-semibold">{{ $product['name'] }}</h3>
                                            <p class="text-sm text-white/60">{{ $product['description'] }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @foreach ($product['swatches'] as $swatch)
                                                <span class="h-7 w-7 rounded-full border border-white/10" style="background: {{ $swatch }}"></span>
                                            @endforeach
                                        </div>
                                        <div class="mt-auto flex items-center justify-between">
                                            <div class="flex items-baseline gap-2 text-white">
                                                <span class="text-xl font-semibold">${{ $product['price'] }}</span>
                                                @if ($product['discount'])
                                                    <span class="text-xs text-white/40 line-through">${{ number_format($product['price'] / (1 - $product['discount'] / 100), 0) }}</span>
                                                @endif
                                            </div>
                                            <button type="button" class="rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold text-white transition hover:border-white/30 hover:bg-white/15">
                                                Add to cart
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col gap-6">
                            @foreach ($displayedProducts as $product)
                                <article class="hover-card flex flex-col gap-6 overflow-hidden rounded-[28px] border border-white/10 bg-white/[0.05] p-6 shadow-[0_30px_60px_rgba(5,15,33,0.35)] transition duration-300 md:flex-row">
                                    <div class="relative h-48 w-full shrink-0 overflow-hidden rounded-3xl md:h-auto md:w-64">
                                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="h-full w-full object-cover transition duration-500 hover:scale-105" loading="lazy">
                                        <div class="absolute left-4 top-4 flex flex-wrap gap-2 text-xs">
                                            <span class="rounded-full bg-white/20 px-3 py-1 font-medium text-white">{{ $product['badge'] }}</span>
                                            @if ($product['discount'])
                                                <span class="rounded-full bg-[#f57b51] px-3 py-1 font-semibold text-white">-{{ $product['discount'] }}%</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex flex-1 flex-col gap-4 text-white/70">
                                        <div class="flex flex-wrap items-center justify-between gap-4 text-xs text-white/40">
                                            <div class="flex items-center gap-3">
                                                <span>{{ $product['brand'] }}</span>
                                                <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1 uppercase tracking-[0.3em]">{{ $product['terrain'] }}</span>
                                            </div>
                                            <span class="flex items-center gap-1 text-[#f8c572]">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                                    <path d="m12 2.5 2.263 6.483h6.612l-5.352 3.891 2.263 6.483L12 15.466l-5.786 3.791 2.263-6.483-5.352-3.891h6.612L12 2.5z" />
                                                </svg>
                                                {{ number_format($product['rating'], 1) }}
                                                <span class="text-xs text-white/40">({{ number_format($product['reviews']) }})</span>
                                            </span>
                                        </div>
                                        <div class="space-y-2 text-white">
                                            <h3 class="text-2xl font-semibold">{{ $product['name'] }}</h3>
                                            <p class="text-sm text-white/60">{{ $product['description'] }}</p>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-3 text-xs text-white/50">
                                            <div class="flex -space-x-2">
                                                @foreach ($product['swatches'] as $swatch)
                                                    <span class="h-8 w-8 rounded-full border border-white/15 ring-2 ring-neutral-950" style="background: {{ $swatch }}"></span>
                                                @endforeach
                                            </div>
                                            <span>Sizes: {{ implode(', ', $product['sizes']) }}</span>
                                            <span>{{ $product['in_stock'] ? 'In stock and ready to ship' : 'Backorder available' }}</span>
                                        </div>
                                        <div class="mt-auto flex flex-wrap items-center justify-between gap-4">
                                            <div class="flex items-baseline gap-2 text-white">
                                                <span class="text-2xl font-semibold">${{ $product['price'] }}</span>
                                                @if ($product['discount'])
                                                    <span class="text-xs text-white/40 line-through">${{ number_format($product['price'] / (1 - $product['discount'] / 100), 0) }}</span>
                                                @endif
                                            </div>
                                            <div class="flex gap-3">
                                                <button type="button" class="rounded-full border border-white/10 bg-white/10 px-5 py-2 text-xs font-semibold text-white transition hover:border-white/30 hover:bg-white/15">
                                                    Add to cart
                                                </button>
                                                <button type="button" class="rounded-full border border-white/10 bg-white/5 px-5 py-2 text-xs font-semibold text-white transition hover:border-white/30 hover:bg-white/10">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if ($this->filteredProducts->count() > $perPage)
                    <div class="flex justify-center">
                        <button type="button" wire:click="loadMore" class="rounded-full border border-white/15 bg-white/10 px-6 py-3 text-sm font-semibold text-white transition hover:border-white/30 hover:bg-white/15">
                            Load more
                        </button>
                    </div>
                @endif
            @endif
        </section>
    </div>
</section>
