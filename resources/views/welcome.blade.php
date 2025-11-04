<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    @include('partials.head', ['title' => 'Shoesify – Step Into Your Next Story'])
    @livewireStyles
</head>

<body class="relative min-h-screen text-white">
    <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-32 left-1/2 h-[420px] w-[420px] -translate-x-1/2 rounded-full blur-[120px]" style="background: rgba(1, 107, 97, 0.3);"></div>
        <div class="absolute bottom-[-180px] left-10 h-[360px] w-[360px] rounded-full bg-[#5470f1]/20 blur-[120px]"></div>
        <div class="absolute right-0 top-24 h-[280px] w-[280px] rounded-full bg-[#2fd3c6]/20 blur-[120px]"></div>
    </div>

    @include('partials.front.nav')

    <main class="mx-auto flex w-full max-w-7xl flex-col gap-24 px-6 pb-24 pt-16 md:px-10">
        <section id="hero" class="grid items-center gap-10 md:grid-cols-[1.1fr_1fr]">
            <div class="space-y-8">
                <span class="pill-badge">
                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-[#4de4d4]"></span>
                    Premium Sneaker Destination
                </span>
                <div class="space-y-4">
                    <h1 class="text-4xl font-semibold leading-tight tracking-tight text-white md:text-5xl">
                        Step into your next story with hyper-modern sneakers crafted for motion.
                    </h1>
                    <p class="max-w-xl text-base text-white/70 md:text-lg">
                        Discover limited drops, curated collections, and responsive experiences that feel as premium as the sneakers you wear. Shoesify delivers luxury vibes with everyday comfort.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="#collections" class="flex items-center gap-2 rounded-full bg-[#4de4d4] px-6 py-3 text-sm font-semibold text-neutral-900 shadow-[0_25px_55px_rgba(77,228,212,0.45)] transition hover:-translate-y-0.5 hover:shadow-[0_35px_70px_rgba(77,228,212,0.55)]">
                        Explore Collections
                        <span aria-hidden="true">→</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="flex items-center gap-2 rounded-full border border-white/20 px-6 py-3 text-sm font-semibold text-white/80 transition hover:border-white/40 hover:text-white">
                        Browse Marketplace
                    </a>
                </div>
                <dl class="grid grid-cols-3 gap-4 rounded-3xl border border-white/10 bg-white/[0.03] p-6 backdrop-blur-xl sm:max-w-xl">
                    <div>
                        <dt class="text-sm text-white/50">Active Members</dt>
                        <dd class="text-2xl font-semibold text-white md:text-3xl">48k+</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-white/50">Drops Per Month</dt>
                        <dd class="text-2xl font-semibold text-white md:text-3xl">120+</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-white/50">Satisfaction</dt>
                        <dd class="flex items-center gap-1 text-2xl font-semibold text-white md:text-3xl">
                            4.9
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 fill-[#f8c572]">
                                <path d="m12 2.5 2.263 6.483h6.612l-5.352 3.891 2.263 6.483L12 15.466l-5.786 3.791 2.263-6.483-5.352-3.891h6.612L12 2.5z" />
                            </svg>
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="relative aspect-[4/5] overflow-hidden rounded-[28px] border border-white/10 bg-gradient-to-br from-white/10 to-white/[0.02] p-1 shadow-[0_45px_120px_rgba(5,15,33,0.55)]">
                <div class="absolute inset-0 bg-grid opacity-40 mix-blend-overlay"></div>
                <div class="relative h-full overflow-hidden rounded-[24px]">
                    <div class="swiper h-full" data-hero-swiper>
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="hero-slide relative flex h-full flex-col justify-between overflow-hidden bg-cover bg-center p-8 text-white" style="background-image: url('https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&q=80&w=1080');">
                                    <div class="absolute inset-0 bg-gradient-to-br from-black/45 via-black/20 to-black/70"></div>
                                    <div class="relative flex items-center justify-between text-sm text-white/70" data-swiper-parallax="-80">
                                        <span class="rounded-full bg-white/10 px-3 py-1">New Arrival</span>
                                        <span>Limited Edition</span>
                                    </div>
                                    <div class="relative space-y-4" data-swiper-parallax="-140">
                                        <h2 class="text-3xl font-semibold tracking-tight md:text-4xl">Aurora Velocity X</h2>
                                        <p class="text-sm text-white/80 md:text-base">
                                            Engineered knit upper dengan adaptive cushioning. Cocok untuk streetwear dan sprint session sekaligus.
                                        </p>
                                        <div class="flex items-center justify-between">
                                            <span class="text-2xl font-semibold">$189</span>
                                            <a href="{{ route('products.index') }}" class="rounded-full bg-white/15 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/25" data-hero-cta>
                                                Lihat Koleksi
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="hero-slide relative flex h-full flex-col justify-between overflow-hidden bg-cover bg-center p-8 text-white" style="background-image: url('https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&q=80&w=1080');">
                                    <div class="absolute inset-0 bg-gradient-to-br from-[#1a2a4f]/70 via-transparent to-black/70"></div>
                                    <div class="relative flex items-center justify-between text-sm text-white/70" data-swiper-parallax="-80">
                                        <span class="rounded-full bg-white/10 px-3 py-1">Just Dropped</span>
                                        <span>Signature Line</span>
                                    </div>
                                    <div class="relative space-y-4" data-swiper-parallax="-140">
                                        <h2 class="text-3xl font-semibold tracking-tight md:text-4xl">Nebula Glide Pro</h2>
                                        <p class="text-sm text-white/80 md:text-base">
                                            Dual-density outsole menjaga kenyamanan sekaligus reflective overlays untuk night run.
                                        </p>
                                        <div class="flex items-center justify-between">
                                            <span class="text-2xl font-semibold">$219</span>
                                            <a href="{{ route('products.index') }}" class="rounded-full bg-white/15 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/25" data-hero-cta>
                                                Lihat Koleksi
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="hero-slide relative flex h-full flex-col justify-between overflow-hidden bg-cover bg-center p-8 text-white" style="background-image: url('https://images.unsplash.com/photo-1511537190424-bbbab87ac5eb?auto=format&q=80&w=1080');">
                                    <div class="absolute inset-0 bg-gradient-to-br from-[#016b61]/60 via-transparent to-black/70"></div>
                                    <div class="relative flex items-center justify-between text-sm text-white/70" data-swiper-parallax="-80">
                                        <span class="rounded-full bg-white/10 px-3 py-1">Featured</span>
                                        <span>Top Rated</span>
                                    </div>
                                    <div class="relative space-y-4" data-swiper-parallax="-140">
                                        <h2 class="text-3xl font-semibold tracking-tight md:text-4xl">Gravity Flux Edge</h2>
                                        <p class="text-sm text-white/80 md:text-base">
                                            Lightweight carbon-fiber plate dengan 20% energy return. Didesain untuk atlet pemburu podium.
                                        </p>
                                        <div class="flex items-center justify-between">
                                            <span class="text-2xl font-semibold">$249</span>
                                            <a href="{{ route('products.index') }}" class="rounded-full bg-white/15 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/25" data-hero-cta>
                                                Lihat Koleksi
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute inset-x-6 bottom-6 z-10 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-white/10 text-white transition hover:bg-white/20" data-hero-prev>
                                    <span class="sr-only">Previous banner</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m15 5-7 7 7 7" />
                                    </svg>
                                </button>
                                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-white/10 text-white transition hover:bg-white/20" data-hero-next>
                                    <span class="sr-only">Next banner</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m9 5 7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                            <div class="flex gap-2" data-hero-pagination></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="glass-panel relative overflow-hidden px-8 py-10">
            <div class="absolute left-10 top-1/2 hidden h-40 w-40 -translate-y-1/2 rounded-full bg-[#4de4d4]/20 blur-3xl md:block"></div>
            <div class="absolute -right-16 top-1/2 hidden h-60 w-60 -translate-y-1/2 rounded-full bg-[#7d5eff]/20 blur-3xl md:block"></div>
            <div class="relative grid gap-6 text-sm text-white/60 md:grid-cols-4 md:text-base">
                <div class="space-y-1">
                    <p class="text-xl font-semibold text-white md:text-2xl">24h Delivery</p>
                    <p>Express parcel partners for Jakarta, Bandung, and Surabaya.</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xl font-semibold text-white md:text-2xl">Authenticity Guaranteed</p>
                    <p>Every pair triple-checked with NFC-powered verification.</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xl font-semibold text-white md:text-2xl">Seamless Exchanges</p>
                    <p>Smart returns with courier pickup & automated restock alerts.</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xl font-semibold text-white md:text-2xl">Member Exclusives</p>
                    <p>Monthly drops, invite-only events, and style concierge.</p>
                </div>
            </div>
        </section>

        <section id="categories" class="space-y-6">
            <header class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div class="space-y-2">
                    <h2 class="section-heading">Explore by style</h2>
                    <p class="section-subtitle">Tap into the vibe that matches your rhythm. Every category is curated by community tastemakers.</p>
                </div>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#4de4d4] transition hover:text-[#7d5eff]">
                    Browse full catalog
                    <span aria-hidden="true">↗</span>
                </a>
            </header>
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4" data-categories>
                @php
                    $categories = [
                        [
                            'title' => 'Urban Utility',
                            'description' => 'Water-resistant uppers, reinforced edges, and reflective panels for city explorers.',
                            'image' => 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&q=80&w=900',
                        ],
                        [
                            'title' => 'Velocity Series',
                            'description' => 'Featherweight racers engineered with kinetic energy cores.',
                            'image' => 'https://images.unsplash.com/photo-1512499629430-78251ff6fcbb?auto=format&q=80&w=900',
                        ],
                        [
                            'title' => 'Studio Essentials',
                            'description' => 'Cushioned streetwear silhouettes in muted earth tones for all-day flex.',
                            'image' => 'https://images.unsplash.com/photo-1491553895911-0055eca6402d?auto=format&q=80&w=900',
                        ],
                        [
                            'title' => 'Altitude Trail',
                            'description' => 'High-performance trail shoes with adaptive traction for multi-terrain control.',
                            'image' => 'https://images.unsplash.com/photo-1539185441755-769473a23570?auto=format&q=80&w=900',
                        ],
                    ];
                @endphp
                @foreach ($categories as $category)
                    <article class="group relative overflow-hidden rounded-[28px] border border-white/10 bg-white/[0.04] p-6 transition duration-500 hover:-translate-y-1 hover:bg-white/[0.08] hover:shadow-[0_28px_60px_rgba(5,15,33,0.45)]">
                        <div class="absolute inset-0 bg-gradient-to-br from-neutral-950/20 via-transparent to-neutral-950/40"></div>
                        <img src="{{ $category['image'] }}" alt="{{ $category['title'] }}" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                        <div class="relative flex h-full flex-col justify-between">
                            <div class="space-y-3">
                                <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 text-white/80 backdrop-blur-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m12 6 7 12H5l7-12z" />
                                    </svg>
                                </span>
                                <h3 class="text-2xl font-semibold text-white">{{ $category['title'] }}</h3>
                                <p class="text-sm text-white/70">{{ $category['description'] }}</p>
                            </div>
                            <div class="mt-5 flex items-center justify-between">
                                <span class="text-sm font-semibold text-[#4de4d4] transition group-hover:text-white">Discover styles</span>
                                <span class="flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-white/10 text-white transition group-hover:translate-x-1 group-hover:bg-white/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section id="collections" class="space-y-6">
            <header class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div class="space-y-2">
                    <h2 class="section-heading">Featured drops</h2>
                    <p class="section-subtitle">Scroll through the hottest silhouettes handpicked by our curators and powered by real-time community demand.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" class="flex h-11 w-11 items-center justify-center rounded-full border border-white/15 bg-white/10 text-white transition hover:bg-white/20" data-scroll-left>
                        <span class="sr-only">Scroll left</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m14 6-6 6 6 6" />
                        </svg>
                    </button>
                    <button type="button" class="flex h-11 w-11 items-center justify-center rounded-full border border-white/15 bg-white/10 text-white transition hover:bg-white/20" data-scroll-right>
                        <span class="sr-only">Scroll right</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m10 6 6 6-6 6" />
                        </svg>
                    </button>
                </div>
            </header>

            @php
                $products = [
                    [
                        'id' => 1,
                        'name' => 'Flux Runner GTR',
                        'price' => 198,
                        'discount' => 20,
                        'rating' => 4.9,
                        'reviews' => 312,
                        'image' => 'https://images.unsplash.com/photo-1515955656352-a1fa3ffcd111?auto=format&q=80&w=900',
                        'colors' => ['#f1f5f9', '#0f172a'],
                        'description' => 'Thermo-regulated mesh with kinetic outsole. Perfect for high-intensity interval training.',
                    ],
                    [
                        'id' => 2,
                        'name' => 'Aero Knit Pulse',
                        'price' => 169,
                        'discount' => 0,
                        'rating' => 4.8,
                        'reviews' => 198,
                        'image' => 'https://images.unsplash.com/photo-1483721310020-03333e577078?auto=format&q=80&w=900',
                        'colors' => ['#fef08a', '#141414'],
                        'description' => 'Breathable upper with responsive strike plate designed for marathon distance.',
                    ],
                    [
                        'id' => 3,
                        'name' => 'Nebula Glide LX',
                        'price' => 229,
                        'discount' => 15,
                        'rating' => 5,
                        'reviews' => 421,
                        'image' => 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&q=80&w=900',
                        'colors' => ['#bae6fd', '#0c4a6e'],
                        'description' => 'Carbon lattice sole enhances every stride with 18% more rebound.',
                    ],
                    [
                        'id' => 4,
                        'name' => 'Orbit Street 2.0',
                        'price' => 149,
                        'discount' => 10,
                        'rating' => 4.7,
                        'reviews' => 132,
                        'image' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&q=80&w=900',
                        'colors' => ['#f8fafc', '#1e293b'],
                        'description' => 'Iconic low-top silhouette reimagined with dynamic cushioning pods.',
                    ],
                    [
                        'id' => 5,
                        'name' => 'Altitude Apex Trail',
                        'price' => 189,
                        'discount' => 0,
                        'rating' => 4.9,
                        'reviews' => 252,
                        'image' => 'https://images.unsplash.com/photo-1504593811423-6dd665756598?auto=format&q=80&w=900',
                        'colors' => ['#f1f5f9', '#14532d'],
                        'description' => 'Hyper-grip outsole and waterproof membrane keep you in control across terrains.',
                    ],
                ];
            @endphp

            <div class="relative">
                <div class="flex snap-x snap-mandatory gap-6 overflow-x-auto pb-6" data-product-track>
                    @foreach ($products as $product)
                        <article class="hover-card group relative flex w-[280px] shrink-0 flex-col overflow-hidden rounded-[28px] border border-white/10 bg-white/[0.05] shadow-[0_30px_60px_rgba(5,15,33,0.45)] transition duration-300" data-product-card data-product='@json($product)'>
                            <div class="relative h-56 overflow-hidden">
                                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                                <button type="button" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/10 text-white transition hover:bg-white/20" data-wishlist>
                                    <span class="sr-only">Toggle wishlist</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 8.25-8.485 8.485a2.121 2.121 0 0 1-3 0L3 8.25" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 6.75c-1.5-1.5-3.75-1.5-5.25 0L12 9l-2.25-2.25c-1.5-1.5-3.75-1.5-5.25 0s-1.5 3.75 0 5.25L12 21l7.5-9a3.708 3.708 0 0 0 0-5.25z" />
                                    </svg>
                                </button>
                                @if ($product['discount'])
                                    <span class="absolute left-4 top-4 rounded-full bg-[#f57b51] px-3 py-1 text-xs font-semibold text-white shadow-lg">-{{ $product['discount'] }}%</span>
                                @endif
                                <button type="button" class="absolute inset-x-4 bottom-4 hidden items-center justify-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-neutral-900 shadow-[0_18px_45px_rgba(255,255,255,0.4)] transition hover:-translate-y-0.5 hover:shadow-[0_28px_65px_rgba(255,255,255,0.45)] group-hover:flex" data-quick-view="{{ $product['id'] }}">
                                    Quick View
                                    <span aria-hidden="true">↗</span>
                                </button>
                            </div>
                            <div class="flex flex-1 flex-col gap-4 p-5">
                                <div class="space-y-1">
                                    <h3 class="text-lg font-semibold">{{ $product['name'] }}</h3>
                                    <p class="text-sm text-white/60">{{ $product['description'] }}</p>
                                </div>
                                <div class="flex items-center gap-1 text-sm text-[#f8c572]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                        <path d="m12 2.5 2.263 6.483h6.612l-5.352 3.891 2.263 6.483L12 15.466l-5.786 3.791 2.263-6.483-5.352-3.891h6.612L12 2.5z" />
                                    </svg>
                                    <span>{{ number_format($product['rating'], 1) }}</span>
                                    <span class="text-white/40">({{ number_format($product['reviews']) }})</span>
                                </div>
                                <div class="mt-auto flex items-center justify-between">
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-xl font-semibold">${{ $product['price'] }}</span>
                                        @if ($product['discount'])
                                            <span class="text-sm text-white/40 line-through">${{ number_format($product['price'] / (1 - $product['discount'] / 100), 0) }}</span>
                                        @endif
                                    </div>
                                    <button type="button" class="relative overflow-hidden rounded-full bg-[#016b61] px-4 py-2 text-sm font-semibold text-white shadow-[0_20px_45px_rgba(1,107,97,0.45)] transition hover:-translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400" data-add-to-cart>
                                        <span class="relative z-10">Add to cart</span>
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="promos" class="grid gap-6 lg:grid-cols-2">
            <article class="glass-panel group relative overflow-hidden p-8 lg:p-10" data-animate>
                <div class="absolute inset-0 bg-gradient-to-br from-[#016b61]/40 via-transparent to-[#1a2a4f]/80 opacity-70 transition duration-700 group-hover:opacity-90"></div>
                <img src="https://images.unsplash.com/photo-1511556670410-f7558fb08ff2?auto=format&q=75&w=1100" alt="Limited Edition Drop" class="absolute inset-0 h-full w-full object-cover opacity-30 transition duration-700 group-hover:scale-105" loading="lazy">
                <div class="relative space-y-6">
                    <span class="pill-badge">Vault Exclusive</span>
                    <div class="space-y-4">
                        <h3 class="text-3xl font-semibold text-white lg:text-4xl">Gravity Flux™ weekend drop</h3>
                        <p class="max-w-lg text-sm text-white/70 lg:text-base">
                            Members get 48-hour priority access with personalized fit recommendations and one-click checkout.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-semibold text-neutral-900 shadow-[0_25px_60px_rgba(255,255,255,0.45)] transition hover:-translate-y-0.5">
                            Claim access
                            <span aria-hidden="true">→</span>
                        </a>
                        <span class="text-sm font-medium text-white/60">Drop closes in <span data-countdown data-countdown-target="{{ now()->addDays(2)->format('c') }}"></span></span>
                    </div>
                </div>
            </article>

            <article class="glass-panel group relative overflow-hidden p-8 lg:p-10" data-animate>
                <div class="absolute inset-0 bg-gradient-to-tl from-[#5470f1]/40 via-transparent to-[#016b61]/70 opacity-70 transition duration-700 group-hover:opacity-90"></div>
                <img src="https://images.unsplash.com/photo-1605733160314-4fc7dac4bb16?auto=format&q=75&w=1100" alt="Shoesify Membership" class="absolute inset-0 h-full w-full object-cover opacity-30 transition duration-700 group-hover:scale-105" loading="lazy">
                <div class="relative space-y-6">
                    <span class="pill-badge">Shoesify One</span>
                    <div class="space-y-4">
                        <h3 class="text-3xl font-semibold text-white lg:text-4xl">Upgrade to limitless perks</h3>
                        <p class="max-w-lg text-sm text-white/70 lg:text-base">
                            Unlock concierge styling, AI-powered fit diagnostics, and early previews for signature collaborations.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-6 py-3 text-sm font-semibold text-white transition hover:border-white/40 hover:bg-white/15">
                            Become a member
                        </a>
                        <span class="text-sm font-medium text-white/60">Starting at <strong class="text-white/90">$9 / month</strong></span>
                    </div>
                </div>
            </article>
        </section>

        <section id="testimonials" class="space-y-6">
            <header class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div class="space-y-2">
                    <h2 class="section-heading">Community pulse</h2>
                    <p class="section-subtitle">Shoesify is co-created with passionate runners, collectors, and creatives worldwide.</p>
                </div>
            </header>

            @php
                $testimonials = [
                    [
                        'name' => 'Farah Lestari',
                        'role' => 'Cyclist & UI Designer',
                        'avatar' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&q=80&w=256',
                        'quote' => '“The onboarding is insanely smooth. I favourite a drop, and the AI stylist instantly builds outfits. The experience feels thoughtful and premium.”',
                        'rating' => 5,
                    ],
                    [
                        'name' => 'Rico Hartanto',
                        'role' => 'Marathoner',
                        'avatar' => 'https://images.unsplash.com/photo-1664574655310-0cf7cdf2bc99?auto=format&q=80&w=256',
                        'quote' => '“Filters update in real-time, even mid-run on my phone. Shoesify nails performance footwear with accurate sizing suggestions.”',
                        'rating' => 5,
                    ],
                    [
                        'name' => 'Amelia Cho',
                        'role' => 'Creative Director',
                        'avatar' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&q=80&w=256',
                        'quote' => '“The quick view modals and 3D rotations are chef’s kiss. I can decide faster without sacrificing detail or aesthetics.”',
                        'rating' => 5,
                    ],
                ];
            @endphp

            <div class="relative overflow-hidden rounded-[32px] border border-white/10 bg-white/[0.04] p-6 md:p-8">
                <div class="absolute left-0 top-0 z-10 h-full w-1/3 pointer-events-none bg-gradient-to-r from-neutral-950/80 via-transparent to-transparent"></div>
                <div class="absolute right-0 top-0 z-10 h-full w-1/3 pointer-events-none bg-gradient-to-l from-neutral-950/80 via-transparent to-transparent"></div>
                <div class="swiper" data-testimonial-swiper>
                    <div class="swiper-wrapper">
                        @foreach ($testimonials as $testimonial)
                            <div class="swiper-slide">
                                <article class="hover-card relative flex h-full min-h-[260px] flex-col gap-5 rounded-[28px] border border-white/10 bg-white/[0.05] p-6 text-white/80 transition duration-500">
                                    <div class="flex items-center gap-4">
                                        <img src="{{ $testimonial['avatar'] }}" alt="{{ $testimonial['name'] }}" class="h-12 w-12 rounded-full object-cover">
                                        <div>
                                            <p class="text-base font-semibold text-white">{{ $testimonial['name'] }}</p>
                                            <p class="text-xs text-white/50">{{ $testimonial['role'] }}</p>
                                        </div>
                                    </div>
                                    <p class="text-sm leading-relaxed text-white/70">{{ $testimonial['quote'] }}</p>
                                    <div class="flex items-center gap-1 text-[#f8c572]">
                                        @for ($i = 0; $i < $testimonial['rating']; $i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                                <path d="m12 2.5 2.263 6.483h6.612l-5.352 3.891 2.263 6.483L12 15.466l-5.786 3.791 2.263-6.483-5.352-3.891h6.612L12 2.5z" />
                                            </svg>
                                        @endfor
                                        <span class="ml-1 text-xs text-white/50">5.0 rated</span>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 flex items-center justify-between">
                        <div class="flex gap-2" data-testimonial-pagination></div>
                        <div class="flex gap-3">
                            <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-white/10 text-white transition hover:bg-white/20" data-testimonial-prev>
                                <span class="sr-only">Testimonial sebelumnya</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m15 5-7 7 7 7" />
                                </svg>
                            </button>
                            <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-white/10 text-white transition hover:bg-white/20" data-testimonial-next>
                                <span class="sr-only">Testimonial berikutnya</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m9 5 7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="glass-panel grid gap-6 overflow-hidden px-8 py-10 lg:grid-cols-[1.1fr_1fr] lg:items-center">
            <div class="space-y-5">
                <span class="pill-badge">Stay in the loop</span>
                <h2 class="text-3xl font-semibold text-white md:text-[34px]">
                    Get curated drops, sizing hacks, and early invites—delivered weekly.
                </h2>
                <p class="text-sm text-white/70 md:text-base">
                    Join 48,000+ collectors receiving handpicked alerts. No spam, only premium insights crafted by the Shoesify team.
                </p>
                <form class="flex flex-col gap-3 sm:flex-row" data-newsletter>
                    <label class="sr-only" for="newsletter-email">Email address</label>
                    <input id="newsletter-email" type="email" required placeholder="Enter your email" class="w-full rounded-full border border-white/15 bg-white/10 px-6 py-3 text-sm text-white placeholder:text-white/40 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/70" />
                    <button type="submit" class="rounded-full bg-[#4de4d4] px-6 py-3 text-sm font-semibold text-neutral-900 shadow-[0_25px_60px_rgba(77,228,212,0.45)] transition hover:-translate-y-0.5">
                        Subscribe
                    </button>
                </form>
                <p class="text-xs text-white/40">By subscribing you agree to receive promo emails. Opt out anytime.</p>
            </div>
            <div class="relative flex items-center justify-center">
                <div class="absolute inset-0 bg-gradient-to-br from-[#4de4d4]/10 to-[#5470f1]/10 blur-3xl"></div>
                <img src="https://images.unsplash.com/photo-1511554951685-215516f0a67b?auto=format&q=80&w=900" alt="Shoesify newsletter preview" class="relative -mb-10 w-full max-w-md rounded-[32px] border border-white/10 bg-white/5 p-3 shadow-[0_30px_80px_rgba(5,15,33,0.55)]">
            </div>
        </section>
    </main>

    @include('partials.front.footer')


    @include('partials.front.quick-view')

    @livewireScripts
    @fluxScripts
</body>
</html>
