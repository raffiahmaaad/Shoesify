<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    @include('partials.head', ['title' => 'Shoesify – Katalog Sneaker'])
    @livewireStyles
</head>

<body class="relative min-h-screen bg-neutral-950 text-white">
    <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-32 left-1/2 h-[420px] w-[420px] -translate-x-1/2 rounded-full blur-[120px]" style="background: rgba(1, 107, 97, 0.35);"></div>
        <div class="absolute bottom-[-180px] right-10 h-[360px] w-[360px] rounded-full bg-[#5470f1]/20 blur-[120px]"></div>
        <div class="absolute left-0 top-24 h-[280px] w-[280px] rounded-full bg-[#2fd3c6]/20 blur-[120px]"></div>
    </div>

    @include('partials.front.nav', [
        'navLinks' => [
            ['label' => 'Katalog', 'href' => route('products.index') . '#katalog'],
            ['label' => 'Homepage', 'href' => route('home')],
            ['label' => 'Promotions', 'href' => route('home') . '#promos'],
            ['label' => 'Reviews', 'href' => route('home') . '#testimonials'],
        ],
    ])

    <main class="mx-auto flex w-full max-w-7xl flex-col gap-16 px-6 pb-24 pt-16 md:px-10">
        <section class="space-y-6">
            <nav aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 text-xs text-white/50">
                    <li>
                        <a href="{{ route('home') }}" class="transition hover:text-white">Home</a>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li>
                        <a href="{{ route('products.index') }}" class="transition hover:text-white">Katalog</a>
                    </li>
                </ol>
            </nav>

            <div class="grid gap-6 lg:grid-cols-[1.2fr_1fr] lg:items-center">
                <div class="space-y-5">
                    <span class="pill-badge">Kurasi minggu ini</span>
                    <h1 class="text-4xl font-semibold leading-tight tracking-tight text-white md:text-5xl">
                        Jelajahi lini sneaker tercanggih dengan filter real-time dan pengalaman quick view yang super mulus.
                    </h1>
                    <p class="max-w-2xl text-sm text-white/70 md:text-base">
                        Katalog Shoesify menggabungkan teknologi Livewire dengan desain future-forward. Cari sesuai kebutuhan, lihat detail warna, dan tambahkan ke keranjang tanpa jeda.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="#katalog" class="inline-flex items-center gap-2 rounded-full bg-[#4de4d4] px-6 py-3 text-sm font-semibold text-neutral-900 shadow-[0_25px_60px_rgba(77,228,212,0.45)] transition hover:-translate-y-0.5">
                            Mulai filter
                            <span aria-hidden="true">→</span>
                        </a>
                        <a href="{{ route('home') }}#collections" class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                            Lihat highlight
                        </a>
                    </div>
                </div>
                <div class="relative">
                    <div class="glass-panel grid gap-4 p-6">
                        <div class="flex items-center justify-between text-sm text-white/70">
                            <span>Live metrics</span>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs">Realtime</span>
                        </div>
                        <div class="grid gap-4 text-white">
                            <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                                <p class="text-xs uppercase tracking-[0.3em] text-white/50">Produk aktif</p>
                                <p class="mt-2 text-2xl font-semibold">12 koleksi</p>
                                <p class="text-xs text-white/50">Diperbarui setiap Senin & Kamis</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                                <p class="text-xs uppercase tracking-[0.3em] text-white/50">Waktu rata-rata</p>
                                <p class="mt-2 text-2xl font-semibold">37 detik</p>
                                <p class="text-xs text-white/50">Mulai filter hingga checkout</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="katalog" class="space-y-10">
            <h2 class="section-heading">Filter & temukan sneaker favoritmu</h2>
            <livewire:products.catalog :search="request('search')" />
        </section>
    </main>

    @include('partials.front.footer')
    @include('partials.front.quick-view')

    @livewireScripts
</body>
</html>
