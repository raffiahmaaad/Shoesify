<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    @include('partials.head', ['title' => 'Checkout – Shoesify'])
    @livewireStyles
</head>

<body class="relative min-h-screen bg-neutral-950 text-white">
    <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-32 left-1/2 h-[420px] w-[420px] -translate-x-1/2 rounded-full blur-[120px]" style="background: rgba(1, 107, 97, 0.35);"></div>
        <div class="absolute bottom-[-220px] right-24 h-[360px] w-[360px] rounded-full bg-[#5470f1]/18 blur-[140px]"></div>
        <div class="absolute left-10 top-40 h-[280px] w-[280px] rounded-full bg-[#2fd3c6]/20 blur-[140px]"></div>
    </div>

    @include('partials.front.nav')

    <main class="mx-auto flex w-full max-w-6xl flex-col gap-12 px-6 pb-24 pt-16 md:px-10">
        <header class="space-y-4 text-center md:text-left">
            <span class="pill-badge w-fit mx-auto md:mx-0">Selesaikan pemesanan dengan 4 langkah mudah</span>
            <div class="space-y-3">
                <h1 class="text-4xl font-semibold leading-tight tracking-tight text-white md:text-5xl">
                    Checkout Shoesify
                </h1>
                <p class="max-w-2xl text-sm text-white/70 md:text-base">
                    Konfirmasi alamat, pilih layanan pengiriman terbaik, dan selesaikan pembayaran melalui Midtrans
                    atau metode manual lain yang kamu sukai. Prosesnya real-time dan aman.
                </p>
            </div>
        </header>

        <livewire:checkout.wizard />
    </main>

    @include('partials.front.footer')

    @livewireScripts
    @fluxScripts
</body>
</html>
