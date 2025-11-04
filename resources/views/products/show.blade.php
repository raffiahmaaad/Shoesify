<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    @include('partials.head', ['title' => $product->meta_title ?: "{$product->name} – Shoesify"])
    @livewireStyles
</head>

@php
    $images = collect($product->images ?? [])->filter()->values();
    $primaryImage = $images->first() ?? 'https://images.unsplash.com/photo-1515955656352-a1fa3ffcd111?auto=format&q=80&w=1200';

    $colorOptions = $product->variants
        ->filter(fn ($variant) => filled($variant->color_name))
        ->map(fn ($variant) => [
            'name' => $variant->color_name,
            'hex' => $variant->color_hex ?? '#f5f5f5',
        ])
        ->unique(fn ($color) => \Illuminate\Support\Str::lower($color['name']))
        ->values();

    $sizeOptions = $product->variants
        ->pluck('size')
        ->filter()
        ->unique()
        ->sort()
        ->values();

    $variantPayload = $product->variants->map(fn ($variant) => [
        'id' => $variant->id,
        'size' => $variant->size,
        'color' => $variant->color_name,
        'hex' => $variant->color_hex,
        'stock' => $variant->stock_quantity,
        'price_adjustment' => $variant->price_adjustment,
        'images' => $variant->images ?? [],
    ]);

    $currentPrice = (int) $product->price;
    $originalPrice = $product->compare_price ?? ($product->discount > 0 ? (int) round($currentPrice / (1 - $product->discount / 100)) : null);

    $productPayload = [
        'id' => $product->id,
        'name' => $product->name,
        'slug' => $product->slug,
        'price' => $currentPrice,
        'originalPrice' => $originalPrice,
        'discount' => $product->discount,
        'rating' => $product->rating,
        'reviews' => $product->reviews,
        'images' => $images,
        'variants' => $variantPayload,
        'sizes' => $sizeOptions,
        'colors' => $colorOptions,
    ];
@endphp

<body class="relative min-h-screen bg-neutral-950 text-white">
    <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-32 left-1/2 h-[420px] w-[420px] -translate-x-1/2 rounded-full blur-[140px]" style="background: rgba(1, 107, 97, 0.35);"></div>
        <div class="absolute bottom-[-220px] right-10 h-[360px] w-[360px] rounded-full bg-[#5470f1]/25 blur-[140px]"></div>
        <div class="absolute left-10 top-24 h-[280px] w-[280px] rounded-full bg-[#2fd3c6]/25 blur-[140px]"></div>
    </div>

    @include('partials.front.nav')

    <main
        class="mx-auto flex w-full max-w-7xl flex-col gap-16 px-6 pb-24 pt-12 md:px-10"
        data-product-detail
        data-product='@json($productPayload)'
    >
        <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs uppercase tracking-[0.3em] text-white/40">
            <a href="{{ route('home') }}" class="transition hover:text-white">Home</a>
            <span>•</span>
            <a href="{{ route('products.index') }}" class="transition hover:text-white">Katalog</a>
            <span>•</span>
            <span class="text-white/70">{{ $product->name }}</span>
        </nav>

        <section class="grid gap-12 lg:grid-cols-[1.1fr_1fr]">
            <div class="space-y-4" data-product-gallery>
                <div class="relative overflow-hidden rounded-[32px] border border-white/10 bg-white/[0.05] p-3 shadow-[0_30px_70px_rgba(5,15,33,0.55)]">
                    <div class="relative overflow-hidden rounded-[26px]">
                        <img src="{{ $primaryImage }}" alt="{{ $product->name }}" data-product-main-image class="h-full w-full object-cover transition duration-500">
                        <button type="button" data-product-zoom class="absolute right-5 top-5 flex h-11 w-11 items-center justify-center rounded-full border border-white/15 bg-white/10 text-white transition hover:bg-white/20">
                            <span class="sr-only">Perbesar gambar</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 21h6m0 0v-6m0 6-8-8m5-2A7 7 0 1 0 3 12a7 7 0 0 0 7 7 6.96 6.96 0 0 0 4.9-2.1" />
                            </svg>
                        </button>
                        @if ($product->discount > 0)
                            <span class="absolute left-5 top-5 rounded-full bg-[#f57b51] px-3 py-1 text-xs font-semibold text-white shadow-lg">
                                -{{ $product->discount }}%
                            </span>
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-5 gap-3 md:grid-cols-6">
                    @foreach ($images as $image)
                        <button
                            type="button"
                            class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 transition hover:border-white/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#4de4d4]"
                            data-product-thumbnail
                            data-image="{{ $image }}"
                        >
                            <img src="{{ $image }}" alt="{{ $product->name }}" class="h-20 w-full object-cover transition duration-500 group-hover:scale-105">
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col gap-10">
                <div class="space-y-4">
                    <div class="flex items-center gap-3 text-xs uppercase tracking-[0.3em] text-[#4de4d4]">
                        <span>{{ optional($product->brand)->name }}</span>
                        @if ($product->is_featured)
                            <span class="inline-flex items-center gap-1 rounded-full border border-[#4de4d4]/30 px-2 py-1 text-[10px] text-[#4de4d4]">
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                Featured Drop
                            </span>
                        @endif
                    </div>
                    <h1 class="text-3xl font-semibold leading-tight tracking-tight text-white md:text-4xl">
                        {{ $product->name }}
                    </h1>
                    <p class="text-sm leading-relaxed text-white/60 md:text-base">
                        {{ $product->short_description ?? \Illuminate\Support\Str::limit($product->description, 180) }}
                    </p>
                    <div class="flex items-center gap-3 text-sm text-white/60">
                        <div class="flex items-center gap-1 text-[#f8c572]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                <path d="m12 2.5 2.263 6.483h6.612l-5.352 3.891 2.263 6.483L12 15.466l-5.786 3.791 2.263-6.483-5.352-3.891h6.612L12 2.5z" />
                            </svg>
                            <span class="font-semibold text-white">{{ number_format($product->rating, 1) }}</span>
                        </div>
                        <span>•</span>
                        <span>{{ number_format($product->reviews) }} ulasan</span>
                        <span>•</span>
                        <span>SKU: {{ $product->sku }}</span>
                    </div>
                </div>

                <div class="space-y-4 rounded-[28px] border border-white/10 bg-white/[0.04] p-6">
                    <div class="flex items-end justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <p class="text-3xl font-semibold text-white" data-product-price>${{ number_format($currentPrice) }}</p>
                                @if ($originalPrice)
                                    <p class="text-lg text-white/40 line-through" data-product-original-price>${{ number_format($originalPrice) }}</p>
                                @endif
                            </div>
                            <p class="text-xs text-white/40">Termasuk garansi orisinal Shoesify</p>
                        </div>
                        <div class="text-right text-xs text-white/50">
                            <p>Tersedia: <span data-product-stock>{{ $product->variants->sum('stock_quantity') }} pasang</span></p>
                            <p>Rilis: {{ optional($product->release_date)->translatedFormat('d F Y') ?? 'Segera' }}</p>
                        </div>
                    </div>

                    @if ($colorOptions->isNotEmpty())
                        <div class="space-y-3">
                            <p class="text-xs uppercase tracking-[0.3em] text-white/40">Warna</p>
                            <div class="flex flex-wrap gap-3">
                                @foreach ($colorOptions as $color)
                                    <button
                                        type="button"
                                        class="group relative flex flex-col items-center gap-2 text-xs text-white/60"
                                        data-variant-color="{{ $color['name'] }}"
                                    >
                                        <span class="flex h-12 w-12 items-center justify-center rounded-full border-2 border-white/20 transition group-[.is-active]:border-white">
                                            <span class="h-9 w-9 rounded-full border border-white/20" style="background-color: {{ $color['hex'] }}"></span>
                                        </span>
                                        <span>{{ $color['name'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($sizeOptions->isNotEmpty())
                        <div class="space-y-3">
                            <p class="text-xs uppercase tracking-[0.3em] text-white/40">Ukuran</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($sizeOptions as $size)
                                    <button
                                        type="button"
                                        class="inline-flex min-w-[56px] items-center justify-center rounded-full border border-white/15 px-4 py-2 text-sm text-white/70 transition hover:border-white/40 hover:text-white data-[disabled='true']:cursor-not-allowed data-[disabled='true']:opacity-40"
                                        data-variant-size="{{ $size }}"
                                    >
                                        {{ $size }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3">
                        <p class="text-xs uppercase tracking-[0.3em] text-white/40">Jumlah</p>
                        <div class="inline-flex items-center rounded-full border border-white/15 bg-white/5 text-white">
                            <button type="button" class="h-10 w-10 text-lg font-semibold" data-quantity-decrease>-</button>
                            <input type="number" min="1" value="1" data-quantity-input class="w-14 bg-transparent text-center text-sm outline-none">
                            <button type="button" class="h-10 w-10 text-lg font-semibold" data-quantity-increase>+</button>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="button" class="flex-1 rounded-full bg-[#4de4d4] px-6 py-3 text-sm font-semibold text-neutral-900 shadow-[0_25px_60px_rgba(77,228,212,0.45)] transition hover:-translate-y-0.5" data-add-to-cart>
                            Tambah ke Keranjang
                        </button>
                        <button type="button" class="flex-1 rounded-full border border-white/15 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10" data-buy-now>
                            Beli Sekarang
                        </button>
                        <button type="button" class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/5 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10" data-wishlist>
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 8.25-8.485 8.485a2.121 2.121 0 0 1-3 0L3 8.25" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 6.75c-1.5-1.5-3.75-1.5-5.25 0L12 9l-2.25-2.25c-1.5-1.5-3.75-1.5-5.25 0s-1.5 3.75 0 5.25L12 21l7.5-9a3.708 3.708 0 0 0 0-5.25z" />
                            </svg>
                            Wishlist
                        </button>
                    </div>

                    <div class="flex flex-wrap gap-3 text-xs text-white/50">
                        <button type="button" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 transition hover:bg-white/10" data-share="whatsapp">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2a9.95 9.95 0 0 0-8.51 15.18L2 22l4.95-1.5A10.01 10.01 0 1 0 12.04 2Zm5.92 14.5c-.26.72-1.52 1.39-2.07 1.38-.55-.02-1-.27-1.45-.47a8.62 8.62 0 0 1-2.63-1.71 9.85 9.85 0 0 1-1.66-2.13 4 4 0 0 1-.84-2.5c0-.66.36-1 .56-1.14.2-.14.48-.2.76-.2h.55c.18 0 .41-.07.64.48.23.55.79 1.9.85 2.04.07.14.12.3.02.49-.09.2-.13.32-.26.5-.13.17-.27.38-.39.51-.13.13-.27.27-.12.53.14.26.63 1.04 1.34 1.68.92.83 1.76 1.1 2.02 1.23.26.13.41.11.56-.07.15-.19.65-.76.82-1.02.17-.26.35-.22.59-.13.25.09 1.57.74 1.83.88.27.13.45.2.52.31.08.11.08.74-.18 1.46Z"/></svg>
                            Share WhatsApp
                        </button>
                        <button type="button" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 transition hover:bg-white/10" data-share="copy">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-2M8 7H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-2M8 7h6" />
                            </svg>
                            Salin Tautan
                        </button>
                    </div>
                </div>

                <div class="rounded-[28px] border border-white/10 bg-white/[0.03] p-6 md:p-8" data-product-tabs>
                    <div class="flex flex-wrap gap-3">
                        @foreach (['description' => 'Deskripsi', 'specs' => 'Spesifikasi', 'reviews' => 'Reviews'] as $key => $label)
                            <button type="button" class="rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-white/60 transition hover:border-white/30 hover:text-white" data-tab-trigger="{{ $key }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    <div class="mt-6 space-y-6">
                        <div data-tab-panel="description" class="text-sm leading-relaxed text-white/70">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                        <div data-tab-panel="specs" class="hidden space-y-4 text-sm text-white/60">
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <p class="text-xs uppercase tracking-[0.3em] text-white/40">Kategori</p>
                                    <p class="mt-1 text-sm text-white">{{ optional($product->category)->name }}</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <p class="text-xs uppercase tracking-[0.3em] text-white/40">Brand</p>
                                    <p class="mt-1 text-sm text-white">{{ optional($product->brand)->name }}</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <p class="text-xs uppercase tracking-[0.3em] text-white/40">Material</p>
                                    <p class="mt-1 text-sm text-white">Performance knit & kinetic outsole</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <p class="text-xs uppercase tracking-[0.3em] text-white/40">Technology</p>
                                    <p class="mt-1 text-sm text-white">Responsive plate • NFC verified • Adaptive cushioning</p>
                                </div>
                            </div>
                        </div>
                        <div data-tab-panel="reviews" class="hidden space-y-5 text-sm text-white/60">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                                <p class="text-sm text-white">Belum ada ulasan.</p>
                                <p class="text-xs text-white/40">Jadilah yang pertama memberikan review setelah checkout.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if ($relatedProducts->isNotEmpty())
            <section class="space-y-6">
                <header class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="section-heading text-2xl md:text-3xl">Kamu mungkin juga suka</h2>
                        <p class="section-subtitle text-sm text-white/60">Pilihan produk selaras dengan preferensi dan kategori yang sama.</p>
                    </div>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#4de4d4] transition hover:text-[#7d5eff]">
                        Lihat semua produk
                        <span aria-hidden="true">↗</span>
                    </a>
                </header>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($relatedProducts as $related)
                        @php
                            $relatedImage = collect($related->images ?? [])->first();
                            $relatedOriginal = $related->discount > 0 ? (int) round($related->price / (1 - $related->discount / 100)) : null;
                        @endphp
                        <article class="hover-card group flex flex-col overflow-hidden rounded-[28px] border border-white/10 bg-white/[0.05] p-5 text-white/70" data-product-card data-product='@json([
                            'id' => $related->id,
                            'name' => $related->name,
                            'price' => $related->price,
                            'discount' => $related->discount,
                            'rating' => $related->rating,
                            'reviews' => $related->reviews,
                            'image' => $relatedImage,
                            'description' => $product->short_description ?? \Illuminate\Support\Str::limit($related->name, 48),
                        ])'>
                            <div class="relative mb-4 aspect-square overflow-hidden rounded-3xl">
                                @if ($relatedImage)
                                    <img src="{{ $relatedImage }}" alt="{{ $related->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-full items-center justify-center bg-white/5 text-xs uppercase tracking-[0.3em] text-white/30">
                                        No Image
                                    </div>
                                @endif
                                <button type="button" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/10 text-white transition hover:bg-white/20" data-wishlist>
                                    <span class="sr-only">Tambah ke wishlist</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 8.25-8.485 8.485a2.121 2.121 0 0 1-3 0L3 8.25" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 6.75c-1.5-1.5-3.75-1.5-5.25 0L12 9l-2.25-2.25c-1.5-1.5-3.75-1.5-5.25 0s-1.5 3.75 0 5.25L12 21l7.5-9a3.708 3.708 0 0 0 0-5.25z" />
                                    </svg>
                                </button>
                            </div>
                            <h3 class="text-lg font-semibold text-white">{{ $related->name }}</h3>
                            <div class="mt-3 flex items-center gap-2 text-sm text-[#f8c572]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                    <path d="m12 2.5 2.263 6.483h6.612l-5.352 3.891 2.263 6.483L12 15.466l-5.786 3.791 2.263-6.483-5.352-3.891h6.612L12 2.5z" />
                                </svg>
                                <span>{{ number_format($related->rating, 1) }}</span>
                            </div>
                            <div class="mt-3 flex items-baseline gap-2 text-white">
                                <span class="text-xl font-semibold">${{ number_format($related->price) }}</span>
                                @if ($relatedOriginal)
                                    <span class="text-sm text-white/40 line-through">${{ number_format($relatedOriginal) }}</span>
                                @endif
                            </div>
                            <div class="mt-6 flex items-center gap-2">
                                <a href="{{ route('products.show', $related->slug) }}" class="inline-flex flex-1 items-center justify-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20">
                                    Lihat Detail
                                </a>
                                <button type="button" class="hidden items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10 group-hover:flex" data-quick-view="{{ $related->id }}">
                                    Quick view
                                </button>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($recentlyViewed->isNotEmpty())
            <section class="space-y-5">
                <header>
                    <h2 class="text-xl font-semibold text-white">Baru saja kamu lihat</h2>
                    <p class="text-sm text-white/50">Lanjutkan eksplorasi tanpa kehilangan jejak item favoritmu.</p>
                </header>
                <div class="flex snap-x snap-mandatory gap-5 overflow-x-auto pb-4" data-product-track>
                    @foreach ($recentlyViewed as $recent)
                        @php
                            $recentImage = collect($recent->images ?? [])->first();
                        @endphp
                        <article class="hover-card group flex w-[240px] shrink-0 flex-col overflow-hidden rounded-[24px] border border-white/10 bg-white/[0.05] p-4 text-white/70">
                            <div class="relative mb-3 h-44 overflow-hidden rounded-2xl">
                                @if ($recentImage)
                                    <img src="{{ $recentImage }}" alt="{{ $recent->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-full items-center justify-center bg-white/5 text-xs uppercase tracking-[0.3em] text-white/30">No Image</div>
                                @endif
                                <span class="absolute left-3 top-3 rounded-full bg-white/10 px-2 py-1 text-[11px] text-white/70">⭐ {{ number_format($recent->rating, 1) }}</span>
                            </div>
                            <h3 class="text-base font-semibold text-white">{{ $recent->name }}</h3>
                            <p class="mt-1 text-sm text-white/50">${{ number_format($recent->price) }}</p>
                            <a href="{{ route('products.show', $recent->slug) }}" class="mt-4 inline-flex items-center justify-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold text-white transition hover:bg-white/10">
                                Lihat detail
                                <span aria-hidden="true">→</span>
                            </a>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    <div class="fixed inset-0 z-[70] hidden items-center justify-center bg-neutral-950/90 px-6 backdrop-blur-lg" data-product-lightbox>
        <button type="button" class="absolute right-10 top-10 flex h-11 w-11 items-center justify-center rounded-full border border-white/10 bg-white/10 text-white transition hover:bg-white/20" data-product-lightbox-close>
            <span class="sr-only">Tutup</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
        <img src="" alt="{{ $product->name }}" class="max-h-[80vh] w-auto max-w-4xl rounded-[32px] border border-white/10 shadow-[0_40px_120px_rgba(5,15,33,0.65)]" data-product-lightbox-image>
    </div>

    <div class="fixed inset-x-0 bottom-4 z-40 w-full px-4 md:hidden">
        <div class="mx-auto flex w-full max-w-md items-center justify-between gap-3 rounded-full border border-white/10 bg-neutral-950/90 px-4 py-3 text-sm text-white/70 opacity-0 shadow-[0_24px_60px_rgba(5,15,33,0.55)] backdrop-blur-xl transition-opacity duration-300" data-product-sticky>
            <div>
                <span class="block text-xs text-white/40">Subtotal</span>
                <span class="text-lg font-semibold text-white" data-sticky-price>${{ number_format($currentPrice) }}</span>
            </div>
            <button type="button" class="inline-flex flex-1 items-center justify-center gap-2 rounded-full bg-[#4de4d4] px-4 py-2 text-sm font-semibold text-neutral-900 shadow-[0_20px_45px_rgba(77,228,212,0.45)] transition hover:-translate-y-0.5" data-sticky-cta>
                Checkout cepat
            </button>
        </div>
    </div>

    @include('partials.front.footer')
    @include('partials.front.quick-view')

    @livewireScripts
    @fluxScripts
</body>
</html>
