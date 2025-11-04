@php
    $navLinks = $navLinks ?? [
        ['label' => 'Produk', 'href' => route('products.index')],
        ['label' => 'Collections', 'href' => route('home') . '#collections'],
        ['label' => 'Categories', 'href' => route('home') . '#categories'],
        ['label' => 'Promotion', 'href' => route('home') . '#promos'],
        ['label' => 'Reviews', 'href' => route('home') . '#testimonials'],
    ];
@endphp

<header
    x-data="headerShell()"
    x-init="init()"
    class="sticky top-0 z-50 border-b border-white/10 bg-gradient-to-b from-neutral-950/95 via-neutral-950/80 to-transparent backdrop-blur-xl"
>
    <div class="mx-auto flex w-full max-w-7xl items-center gap-4 px-6 py-5 md:px-10">
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-semibold tracking-tight text-white">
            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10 text-xl text-white shadow-[0_20px_40px_rgba(1,107,97,0.35)]">
                S
            </span>
            Shoesify
        </a>

        <nav class="hidden flex-1 items-center gap-6 text-sm font-medium text-white/70 lg:flex">
            @foreach ($navLinks as $link)
                <a href="{{ $link['href'] }}" class="transition-colors hover:text-white">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex flex-1 items-center justify-end gap-2 md:gap-3">
            <button
                type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/10 text-white transition hover:bg-white/10 lg:hidden"
                x-on:click="toggleSearch()"
            >
                <span class="sr-only">Buka pencarian</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z" />
                </svg>
            </button>

            <div class="hidden flex-1 lg:flex">
                <livewire:header-search />
            </div>

            <button
                type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/10 text-white transition hover:bg-white/10"
                x-on:click="toggleTheme()"
                :aria-pressed="theme === 'dark'"
            >
                <span class="sr-only">Toggle dark mode</span>
                <svg x-show="theme === 'light'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v2.25M12 18.75V21M4.22 4.22l1.59 1.59M18.19 18.19l1.59 1.59M3 12h2.25M18.75 12H21M5.81 18.19l-1.59 1.59M19.41 4.22l-1.59 1.59M12 6.75a5.25 5.25 0 1 1 0 10.5 5.25 5.25 0 0 1 0-10.5z" />
                </svg>
                <svg x-show="theme === 'dark'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 0 0 9.79 9.79z"></path>
                </svg>
            </button>

            <div class="hidden md:block">
                <livewire:cart.indicator />
            </div>

            <div x-data="{ open: false }" class="relative">
                <button
                    type="button"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white transition hover:bg-white/10"
                    x-on:click="open = !open"
                    x-bind:aria-expanded="open"
                >
                    <span class="sr-only">Menu pengguna</span>
                    @auth
                        <span class="text-sm font-semibold">{{ auth()->user()->initials() }}</span>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.5 20.118a7.5 7.5 0 0 1 15 0A17.933 17.933 0 0 1 12 21.75c-2.7 0-5.25-.594-7.5-1.632z" />
                        </svg>
                    @endauth
                </button>

                <div
                    x-show="open"
                    x-transition
                    x-on:click.away="open = false"
                    class="absolute right-0 z-50 mt-3 w-64 overflow-hidden rounded-3xl border border-white/10 bg-neutral-950/95 text-sm text-white/70 shadow-[0_24px_60px_rgba(5,15,33,0.55)] backdrop-blur-xl"
                    style="display: none;"
                >
                    <div class="border-b border-white/5 px-4 py-4">
                        @auth
                            <p class="text-xs uppercase tracking-[0.3em] text-white/40">Halo</p>
                            <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                        @else
                            <p class="text-sm text-white">Masuk atau daftar untuk pengalaman personal.</p>
                        @endauth
                    </div>
                    <nav class="flex flex-col divide-y divide-white/5">
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-4 py-3 transition hover:bg-white/5 hover:text-white">Akun Saya</a>
                            <a href="#" class="px-4 py-3 transition hover:bg-white/5 hover:text-white">Pesanan Saya</a>
                            <a href="#" class="px-4 py-3 transition hover:bg-white/5 hover:text-white">Wishlist</a>
                            <a href="#" class="px-4 py-3 transition hover:bg-white/5 hover:text-white">Notifikasi</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2 px-4 py-3 text-left text-rose-400 transition hover:bg-rose-500/10 hover:text-rose-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3H6.75A2.25 2.25 0 0 0 4.5 5.25v13.5A2.25 2.25 0 0 0 6.75 21h6.75a2.25 2.25 0 0 0 2.25-2.25V15M12 15l3-3m0 0-3-3m3 3H3" />
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-3 transition hover:bg-white/5 hover:text-white">Login</a>
                            <a href="{{ route('register') }}" class="px-4 py-3 transition hover:bg-white/5 hover:text-white">Daftar</a>
                        @endauth
                    </nav>
                </div>
            </div>

            <button
                type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/10 text-white transition hover:bg-white/10 lg:hidden"
                data-mobile-toggle
                aria-expanded="false"
                aria-controls="mobile-nav"
            >
                <span class="sr-only">Toggle menu</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </button>
        </div>
    </div>

    <div
        x-show="mobileSearch"
        x-transition
        class="border-t border-white/10 bg-neutral-950/95 px-6 pb-6 pt-4 lg:hidden"
        data-mobile-search
        style="display: none;"
    >
        <livewire:header-search />
    </div>

    <div id="mobile-nav" class="hidden border-t border-white/10 px-6 pb-6 lg:hidden" data-mobile-menu>
        <nav class="flex flex-col gap-4 text-sm font-medium text-white/70">
            @foreach ($navLinks as $link)
                <a href="{{ $link['href'] }}" class="transition-colors hover:text-white">
                    {{ $link['label'] }}
                </a>
            @endforeach
            <div class="flex items-center gap-3 pt-4">
                <livewire:cart.indicator />
                <button
                    type="button"
                    class="inline-flex flex-1 items-center justify-center rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10"
                    x-on:click="toggleTheme()"
                >
                    <span x-show="theme === 'light'">Mode Gelap</span>
                    <span x-show="theme === 'dark'">Mode Terang</span>
                </button>
            </div>
            @guest
                <a href="{{ route('login') }}" class="pt-3 text-white/70 transition hover:text-white">Sign In</a>
                <a href="{{ route('register') }}" class="rounded-full bg-white/10 px-5 py-2 text-center text-sm font-semibold text-white shadow-inner shadow-white/10 transition hover:bg-white/15">
                    Create Account
                </a>
            @endguest
        </nav>
    </div>
</header>
