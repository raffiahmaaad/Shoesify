@php
    use App\Models\Category;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Str;

    $navLinks = [
        [
            'label' => 'Product',
            'href' => route('products.index'),
            'active' => request()->routeIs('products.*'),
        ],
        [
            'label' => 'Collections',
            'href' => route('home') . '#collections',
            'active' => request()->routeIs('home'),
        ],
        [
            'label' => 'Promotion',
            'href' => route('home') . '#promos',
            'active' => request()->routeIs('home'),
        ],
    ];

    $navCategories = Cache::remember(
        'nav.categories',
        now()->addMinutes(30),
        fn () => Category::query()
            ->select(['id', 'name', 'slug', 'description', 'image'])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderByRaw('COALESCE(sort_order, 9999)')
            ->orderBy('name')
            ->limit(8)
            ->get()
    );
@endphp

<header x-data="headerShell()" x-init="init()"
    class="sticky top-0 z-50 border-b border-white/10 bg-gradient-to-b from-neutral-950/95 via-neutral-950/80 to-transparent backdrop-blur-xl dark:from-zinc-950/95 dark:via-zinc-950/80">
    <div class="mx-auto flex w-full max-w-7xl items-center gap-3 px-6 py-5 md:gap-4 md:px-10">
        <div class="flex flex-1 items-center gap-3">
            <button type="button"
                class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white transition hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-neutral-950 dark:border-zinc-800 dark:bg-zinc-900/80 dark:text-zinc-100 dark:hover:bg-zinc-800/80 dark:focus-visible:ring-zinc-500/70 lg:hidden"
                x-on:click="toggleMobileNav()" x-bind:aria-expanded="mobileNav" aria-controls="mobile-nav">
                <span class="sr-only">Buka navigasi</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <a href="{{ route('home') }}"
                class="flex items-center gap-2 text-lg font-semibold tracking-tight text-white transition hover:text-white/90 dark:text-zinc-50">
                <span
                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10 text-xl text-white shadow-[0_20px_40px_rgba(1,107,97,0.35)] dark:bg-zinc-900/80 dark:text-zinc-100">
                    S
                </span>
                Shoesify
            </a>
        </div>

        <nav
            class="hidden flex-1 items-center justify-center gap-6 text-[0.95rem] font-semibold tracking-tight text-white/70 lg:flex">
            @foreach ($navLinks as $link)
                @php
                    $isActive = $link['active'];
                @endphp
                <a href="{{ $link['href'] }}"
                    class="inline-flex items-center justify-center gap-2 rounded-full px-5 py-2 transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-neutral-950 {{ $isActive ? 'bg-white/10 text-white shadow-[0_16px_32px_rgba(5,15,33,0.35)] dark:bg-zinc-900/80 dark:text-zinc-100' : 'text-white/70 hover:-translate-y-0.5 hover:bg-white/10 hover:text-white dark:text-zinc-400 dark:hover:bg-zinc-800/80 dark:hover:text-zinc-100' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach

            <div x-data="{ open: false }" class="relative">
                <button type="button"
                    class="inline-flex items-center gap-2 rounded-full px-5 py-2 text-white/70 transition-all duration-200 hover:-translate-y-0.5 hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-neutral-950 dark:text-zinc-400 dark:hover:bg-zinc-800/80 dark:hover:text-zinc-100"
                    x-on:click="open = !open" x-bind:aria-expanded="open">
                    Categories
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="m6 9 6 6 6-6" />
                    </svg>
                </button>

                <div x-cloak x-show="open" x-transition x-on:click.away="open = false"
                    class="glass-dropdown absolute left-1/2 z-50 mt-3 hidden w-[520px] -translate-x-1/2 overflow-hidden border border-white/10 bg-neutral-950/90 text-sm shadow-[0_42px_120px_rgba(5,15,33,0.6)] backdrop-blur-2xl dark:border-zinc-800 dark:bg-zinc-950/95 lg:block">
                    <div class="grid grid-cols-2 gap-4 p-5">
                        @forelse ($navCategories as $category)
                            <a href="{{ route('categories.show', $category->slug) }}"
                                class="group flex items-center gap-4 rounded-2xl border border-white/5 bg-white/5 p-3 transition hover:-translate-y-0.5 hover:border-white/20 hover:bg-white/10 dark:border-zinc-800/80 dark:bg-zinc-900/60 dark:hover:border-zinc-600 dark:hover:bg-zinc-900/80">
                                <div class="relative h-14 w-14 overflow-hidden rounded-2xl bg-white/10 dark:bg-zinc-800">
                                    @if ($category->image)
                                        <img src="{{ $category->image }}" alt="{{ $category->name }}"
                                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105 dark:brightness-90">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-xs text-white/60 dark:text-zinc-400">
                                            {{ Str::upper(Str::substr($category->name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm font-semibold text-white dark:text-zinc-100">
                                        {{ $category->name }}
                                    </p>
                                    @if ($category->description)
                                        <p class="text-xs text-white/60 line-clamp-2 dark:text-zinc-400">
                                            {{ $category->description }}
                                        </p>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <div class="col-span-2 rounded-2xl border border-white/10 bg-white/5 p-4 text-center text-white/60 dark:border-zinc-800/80 dark:bg-zinc-900/70 dark:text-zinc-400">
                                Belum ada kategori aktif.
                            </div>
                        @endforelse
                    </div>
                    <div class="border-t border-white/10 bg-white/5 px-5 py-3 text-right text-xs text-white/60 dark:border-zinc-800/80 dark:bg-zinc-900/60 dark:text-zinc-400">
                        Temukan koleksi lengkap sesuai kategori favoritmu.
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex flex-1 items-center justify-end gap-2 md:gap-3">
            <button type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/10 text-white transition hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-neutral-950 dark:border-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-800/80 dark:focus-visible:ring-zinc-500/70 lg:hidden"
                x-on:click="toggleSearch()">
                <span class="sr-only">Buka pencarian</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z" />
                </svg>
            </button>

            <div class="hidden flex-1 lg:flex">
                <livewire:header-search />
            </div>

            <div class="hidden md:block" x-data="darkToggle()">
                <flux:button x-bind:icon="dark ? 'sun' : 'moon'" variant="subtle" aria-label="Toggle dark mode"
                    class="h-11 w-11 shrink-0 !rounded-full border border-white/10 bg-white/5 text-white hover:bg-white/15 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-neutral-950 dark:border-zinc-700 dark:bg-zinc-900/80 dark:text-zinc-100 dark:hover:bg-zinc-800/80 dark:focus-visible:ring-zinc-500/70"
                    x-on:click="toggle()" />
            </div>

            <div class="hidden md:block">
                <livewire:cart.indicator />
            </div>

            <div x-data="{ open: false }" class="relative">
                <button type="button"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white transition hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-neutral-950 dark:border-zinc-800 dark:bg-zinc-900/80 dark:text-zinc-100 dark:hover:bg-zinc-800/80 dark:focus-visible:ring-zinc-500/70"
                    x-on:click="open = !open" x-bind:aria-expanded="open">
                    <span class="sr-only">Menu pengguna</span>
                    @auth
                        <span class="text-sm font-semibold">{{ auth()->user()->initials() }}</span>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.5 20.118a7.5 7.5 0 0 1 15 0A17.933 17.933 0 0 1 12 21.75c-2.7 0-5.25-.594-7.5-1.632z" />
                        </svg>
                    @endauth
                </button>

                <div x-cloak x-show="open" x-transition x-on:click.away="open = false"
                    class="glass-dropdown absolute right-0 z-50 mt-3 w-64 overflow-hidden text-sm">
                    <div class="border-b border-white/5 bg-white/5 px-4 py-4 dark:border-zinc-800/70 dark:bg-zinc-900/70">
                        @auth
                            <p class="text-xs uppercase tracking-[0.3em] text-white/40 dark:text-zinc-500">Halo,
                                {{ auth()->user()->name }}</p>
                            <p class="text-sm font-semibold text-white dark:text-zinc-100">Selamat datang kembali!</p>
                        @else
                            <p class="text-xs text-white/60 dark:text-zinc-400">Masuk untuk pengalaman personal.</p>
                        @endauth
                    </div>

                    <div class="space-y-1 bg-white/5 px-4 py-4 dark:bg-zinc-900/70">
                        @auth
                            <a href="{{ route('account.profile') }}"
                                class="flex items-center justify-between rounded-2xl px-3 py-2 text-white/80 transition hover:bg-white/10 hover:text-white dark:text-zinc-300 dark:hover:bg-zinc-800/70 dark:hover:text-zinc-100">
                                <span>Akun Saya</span>
                                <span aria-hidden="true">→</span>
                            </a>
                            <a href="{{ route('account.orders') }}"
                                class="flex items-center justify-between rounded-2xl px-3 py-2 text-white/80 transition hover:bg-white/10 hover:text-white dark:text-zinc-300 dark:hover:bg-zinc-800/70 dark:hover:text-zinc-100">
                                <span>Pesanan Saya</span>
                                <span aria-hidden="true">→</span>
                            </a>
                            <a href="{{ route('account.wishlist') }}"
                                class="flex items-center justify-between rounded-2xl px-3 py-2 text-white/80 transition hover:bg-white/10 hover:text-white dark:text-zinc-300 dark:hover:bg-zinc-800/70 dark:hover:text-zinc-100">
                                <span>Wishlist</span>
                                <span aria-hidden="true">→</span>
                            </a>
                            <div class="border-t border-white/10 pt-3 dark:border-zinc-800/70">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex w-full items-center justify-between rounded-2xl px-3 py-2 text-left text-white/70 transition hover:bg-white/10 hover:text-white dark:text-zinc-400 dark:hover:bg-zinc-800/70 dark:hover:text-zinc-100">
                                        <span>Logout</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="m15 12-3 3m3-3-3-3m3 3H3m12-7.5V6a3 3 0 0 1 3 3v6a3 3 0 0 1-3 3v1.5" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}"
                                class="flex items-center justify-between rounded-2xl px-3 py-2 text-white/80 transition hover:bg-white/10 hover:text-white dark:text-zinc-300 dark:hover:bg-zinc-800/70 dark:hover:text-zinc-100">
                                <span>Sign In</span>
                                <span aria-hidden="true">→</span>
                            </a>
                            <a href="{{ route('register') }}"
                                class="flex items-center justify-between rounded-2xl px-3 py-2 text-white/80 transition hover:bg-white/10 hover:text-white dark:text-zinc-300 dark:hover:bg-zinc-800/70 dark:hover:text-zinc-100">
                                <span>Buat Akun</span>
                                <span aria-hidden="true">→</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-cloak x-show="mobileNav" x-transition x-on:keydown.escape.window="closeMobileNav()"
        class="border-t border-white/10 bg-neutral-950/95 px-6 pb-6 shadow-[0_36px_120px_rgba(5,15,33,0.65)] dark:border-zinc-800 dark:bg-zinc-950/95 lg:hidden"
        id="mobile-nav">
        <nav class="flex flex-col gap-4 text-sm font-medium text-white/80 dark:text-zinc-300">
            @foreach ($navLinks as $link)
                <a href="{{ $link['href'] }}"
                    class="rounded-2xl px-4 py-2 transition hover:bg-white/10 hover:text-white dark:hover:bg-zinc-800/70 dark:hover:text-zinc-100"
                    x-on:click="closeMobileNav()">
                    {{ $link['label'] }}
                </a>
            @endforeach

            @if ($navCategories->isNotEmpty())
                <div x-data="{ open: false }" class="rounded-2xl border border-white/10 bg-white/5 dark:border-zinc-800 dark:bg-zinc-900/70">
                    <button type="button"
                        class="flex w-full items-center justify-between px-4 py-2 text-left text-white/80 dark:text-zinc-300"
                        x-on:click="open = !open" x-bind:aria-expanded="open">
                        Categories
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition" x-bind:class="open ? 'rotate-180' : ''"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="m6 9 6 6 6-6" />
                        </svg>
                    </button>
                    <div x-cloak x-show="open" x-transition
                        class="border-t border-white/10 px-4 py-3 text-sm dark:border-zinc-800">
                        <ul class="space-y-2">
                            @foreach ($navCategories as $category)
                                <li>
                                    <a href="{{ route('categories.show', $category->slug) }}"
                                        class="flex items-center gap-3 rounded-xl px-2 py-2 text-white/80 transition hover:bg-white/10 hover:text-white dark:text-zinc-300 dark:hover:bg-zinc-800/70 dark:hover:text-zinc-100"
                                        x-on:click="closeMobileNav()">
                                        @if ($category->image)
                                            <img src="{{ $category->image }}" alt="{{ $category->name }}"
                                                class="h-9 w-9 rounded-xl object-cover dark:brightness-90">
                                        @else
                                            <span
                                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 text-xs text-white/60 dark:bg-zinc-800/80 dark:text-zinc-400">
                                                {{ Str::upper(Str::substr($category->name, 0, 2)) }}
                                            </span>
                                        @endif
                                        <span>{{ $category->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="flex items-center gap-3 pt-4">
                <livewire:cart.indicator />
                <div class="flex-1" x-data="darkToggle()">
                    <flux:button x-bind:icon="dark ? 'sun' : 'moon'" variant="subtle" aria-label="Toggle dark mode"
                        class="flex w-full justify-center !rounded-full border border-white/10 bg-white/5 text-white hover:bg-white/15 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-neutral-950 dark:border-zinc-700 dark:bg-zinc-900/80 dark:text-zinc-100 dark:hover:bg-zinc-800/80 dark:focus-visible:ring-zinc-500/70"
                        x-on:click="toggle()" />
                </div>
            </div>

            @guest
                <a href="{{ route('login') }}"
                    class="rounded-2xl px-4 pt-4 text-white/70 transition hover:text-white dark:text-zinc-400 dark:hover:text-zinc-100"
                    x-on:click="closeMobileNav()">Sign In</a>
                <a href="{{ route('register') }}"
                    class="rounded-full bg-white/10 px-5 py-2 text-center text-sm font-semibold text-white shadow-inner shadow-white/10 transition hover:bg-white/15 dark:bg-zinc-900/80 dark:text-zinc-100 dark:hover:bg-zinc-800/80"
                    x-on:click="closeMobileNav()">Create Account</a>
            @endguest
        </nav>
    </div>
</header>
