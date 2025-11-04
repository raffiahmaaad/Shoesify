@props([
    'title' => null,
])

@php
    $links = [
        ['label' => 'Pesanan', 'route' => 'account.orders', 'icon' => '🧾'],
        ['label' => 'Profil', 'route' => 'account.profile', 'icon' => '👤'],
        ['label' => 'Alamat', 'route' => 'account.addresses', 'icon' => '📍'],
        ['label' => 'Wishlist', 'route' => 'account.wishlist', 'icon' => '❤️'],
    ];
@endphp

<div class="grid gap-10 lg:grid-cols-[240px_minmax(0,1fr)]">
    <aside class="glass-panel h-max space-y-6 p-6">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-white/40">Akun Shoesify</p>
            <p class="mt-1 text-lg font-semibold text-white">{{ auth()->user()->name }}</p>
            <p class="text-xs text-white/50">{{ auth()->user()->email }}</p>
        </div>
        <nav class="flex flex-col gap-1 text-sm font-medium text-white/60">
            @foreach ($links as $link)
                @php
                    $active = request()->routeIs($link['route']) || request()->routeIs($link['route'] . '.*');
                @endphp
                <a
                    href="{{ route($link['route']) }}"
                    wire:navigate
                    @class([
                        'inline-flex items-center justify-between gap-3 rounded-2xl px-4 py-3 transition',
                        'bg-[#4de4d4]/15 text-white' => $active,
                        'bg-white/5 hover:bg-white/10 text-white/70' => ! $active,
                    ])
                >
                    <span class="flex items-center gap-3">
                        <span class="text-base">{{ $link['icon'] }}</span>
                        <span>{{ $link['label'] }}</span>
                    </span>
                    @if ($active)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m9 5 7 7-7 7" />
                        </svg>
                    @endif
                </a>
            @endforeach
        </nav>
        <p class="text-[11px] leading-relaxed text-white/50">
            Kelola setiap pesanan, alamat, dan wishlist di satu tempat. Semua perubahan tersinkron otomatis dengan checkout.
        </p>
    </aside>

    <main class="space-y-6">
        @if ($title || $attributes->has('subtitle'))
            <header class="space-y-2">
                @if ($title)
                    <h1 class="text-2xl font-semibold text-white">{{ $title }}</h1>
                @endif
                @if ($subtitle = $attributes->get('subtitle'))
                    <p class="text-sm text-white/60">{{ $subtitle }}</p>
                @endif
            </header>
        @endif
        <div class="space-y-6">
            {{ $slot }}
        </div>
    </main>
</div>
