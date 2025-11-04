<div
    x-data="{
        notice: @entangle('flashMessage'),
        init() {
            this.$watch('notice', value => {
                if (value) {
                    clearTimeout(this.__timer);
                    this.__timer = setTimeout(() => this.notice = null, 4200);
                }
            });
        }
    }"
    class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_360px]"
>
    <div class="space-y-6">
        <template x-if="notice">
            <div
                class="glass-panel flex items-start justify-between gap-4 border-l-4 border-[#4de4d4] px-5 py-4 text-sm text-white/80"
                x-transition
            >
                <p x-text="notice"></p>
                <button type="button" class="text-white/50 transition hover:text-white" x-on:click="notice = null">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>

        @if (empty($cart['items']))
            <div class="glass-panel flex flex-col items-center gap-4 px-8 py-16 text-center text-white/70">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-dashed border-white/25">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m3 3 1.88 1.88A2 2 0 0 0 6.285 6H19.5a1 1 0 0 1 .969 1.243l-1.5 6A1 1 0 0 1 18 14H7.165a2 2 0 0 1-1.948-1.561L3.28 4.879M12 19a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" />
                    </svg>
                </div>
                <div class="space-y-2">
                    <h2 class="text-xl font-semibold text-white">Keranjangmu masih kosong</h2>
                    <p class="text-sm text-white/60">Mulai eksplor koleksi unggulan Shoesify dan tambahkan sneaker favoritmu.</p>
                </div>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">
                        Jelajahi katalog
                        <span aria-hidden="true">→</span>
                    </a>
                    <a href="{{ route('home') }}#collections" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">
                        Lihat highlight
                    </a>
                </div>
            </div>
        @else
            <section class="glass-panel space-y-6 p-6">
                <header class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-white">Ringkasan Produk</h2>
                        <p class="text-sm text-white/60">Sesuaikan kuantitas atau simpan untuk nanti sebelum checkout.</p>
                    </div>
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white/70">
                        {{ count($cart['items']) }} item
                    </span>
                </header>

                <div class="divide-y divide-white/10">
                    @foreach ($cart['items'] as $item)
                        <article
                            wire:key="cart-item-{{ $item['id'] }}"
                            x-data="{ qty: {{ $item['quantity'] }}, animating: false }"
                            x-effect="animating = true; setTimeout(() => animating = false, 320)"
                            class="flex flex-col gap-4 py-5 lg:flex-row lg:items-center"
                        >
                            <div class="flex items-center gap-4">
                                <div class="relative h-20 w-20 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
                                    @if ($item['image'])
                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-[10px] text-white/40">No Image</div>
                                    @endif
                                </div>
                                <div class="flex flex-col gap-1 text-sm text-white/70">
                                    <a href="{{ $item['slug'] ? route('products.show', $item['slug']) : '#' }}" class="text-base font-semibold text-white transition hover:text-[#4de4d4]">
                                        {{ $item['name'] }}
                                    </a>
                                    @if ($item['brand'])
                                        <span class="text-xs uppercase tracking-[0.3em] text-white/40">{{ $item['brand'] }}</span>
                                    @endif
                                    @if ($item['variant_label'])
                                        <span class="text-xs text-white/50">{{ $item['variant_label'] }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-1 flex-col gap-4 lg:flex-row lg:items-center lg:justify-end">
                                <div class="flex items-center gap-3">
                                    <button
                                        type="button"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white transition hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-40"
                                        wire:click="decrementQuantity({{ $item['id'] }})"
                                        wire:loading.attr="disabled"
                                        wire:target="decrementQuantity({{ $item['id'] }})"
                                    >
                                        <span class="sr-only">Kurangi jumlah</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14" />
                                        </svg>
                                    </button>
                                    <input
                                        type="number"
                                        min="1"
                                        class="w-16 rounded-full border border-white/10 bg-white/5 py-2 text-center text-sm font-semibold text-white focus:border-[#4de4d4]/70 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/40"
                                        value="{{ $item['quantity'] }}"
                                        wire:change="updateQuantity({{ $item['id'] }}, $event.target.value)"
                                    >
                                    <button
                                        type="button"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white transition hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-40"
                                        wire:click="incrementQuantity({{ $item['id'] }})"
                                        wire:loading.attr="disabled"
                                        wire:target="incrementQuantity({{ $item['id'] }})"
                                    >
                                        <span class="sr-only">Tambah jumlah</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 5v14m-7-7h14" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="flex min-w-[140px] flex-col text-right text-sm">
                                    <span class="font-semibold text-white">Rp {{ number_format($item['line_total'], 0, ',', '.') }}</span>
                                    <span class="text-xs text-white/50">Rp {{ number_format($item['unit_price'], 0, ',', '.') }} / item</span>
                                </div>

                                <div class="flex gap-3 text-xs text-white/50">
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 transition hover:bg-white/10 hover:text-white"
                                        wire:click="saveForLater({{ $item['id'] }})"
                                        wire:target="saveForLater({{ $item['id'] }})"
                                        wire:loading.attr="disabled"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5.882C5 4.274 6.386 3 8.096 3c.86 0 1.685.346 2.288.962l.598.61.598-.61A3.243 3.243 0 0 1 13.868 3c1.71 0 3.096 1.274 3.096 2.882 0 .768-.294 1.505-.822 2.051L12 14.25 5.822 7.933A2.96 2.96 0 0 1 5 5.882Z" />
                                        </svg>
                                        Simpan nanti
                                    </button>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 transition hover:bg-rose-500/20 hover:text-rose-200"
                                        wire:click="removeItem({{ $item['id'] }})"
                                        wire:target="removeItem({{ $item['id'] }})"
                                        wire:loading.attr="disabled"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        @if (!empty($cart['saved_items']))
            <section class="glass-panel space-y-4 p-6">
                <header class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">Disimpan untuk nanti</h2>
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs text-white/60">{{ count($cart['saved_items']) }} item</span>
                </header>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($cart['saved_items'] as $item)
                        <article wire:key="saved-item-{{ $item['id'] }}" class="flex gap-4 rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                            <div class="h-16 w-16 overflow-hidden rounded-2xl bg-white/10">
                                @if ($item['image'])
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col gap-2 text-sm text-white/70">
                                <p class="font-semibold text-white">{{ $item['name'] }}</p>
                                @if ($item['variant_label'])
                                    <span class="text-xs text-white/50">{{ $item['variant_label'] }}</span>
                                @endif
                                <div class="mt-auto flex items-center justify-between text-xs text-white/60">
                                    <span>Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</span>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 transition hover:bg-white/10 hover:text-white"
                                        wire:click="moveToCart({{ $item['id'] }})"
                                        wire:target="moveToCart({{ $item['id'] }})"
                                        wire:loading.attr="disabled"
                                    >
                                        Kembalikan ke keranjang
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="glass-panel space-y-5 p-6">
            <header>
                <h2 class="text-lg font-semibold text-white">Kupon & promosi</h2>
                <p class="text-sm text-white/60">Punya kode khusus? Masukkan di sini untuk menikmati potongan harga.</p>
            </header>
            @if ($cart['coupon']['code'])
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    <div class="flex items-center gap-3">
                        <span class="rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-emerald-200">
                            {{ $cart['coupon']['code'] }}
                        </span>
                        <span>{{ $cart['coupon']['label'] }}</span>
                    </div>
                    <button type="button" class="text-xs uppercase tracking-[0.3em] transition hover:text-white" wire:click="removeCoupon" wire:loading.attr="disabled">Batalkan</button>
                </div>
            @else
                <div class="flex flex-col gap-3 md:flex-row">
                    <div class="flex-1 rounded-3xl border border-white/10 bg-white/5 px-5 py-3">
                        <input
                            type="text"
                            placeholder="Masukkan kode kupon"
                            class="w-full bg-transparent text-sm text-white placeholder:text-white/40 focus:outline-none"
                            wire:model.defer="couponCode"
                        >
                    </div>
                    <button
                        type="button"
                        class="inline-flex items-center justify-center gap-2 rounded-full bg-[#4de4d4] px-6 py-3 text-sm font-semibold text-neutral-900 transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-50"
                        wire:click="applyCoupon"
                        wire:loading.attr="disabled"
                        wire:target="applyCoupon"
                    >
                        Terapkan kupon
                        <span aria-hidden="true">→</span>
                    </button>
                </div>
            @endif
            <p class="text-xs text-white/40">Kupon tertentu memiliki minimal belanja atau batas penggunaan. Pastikan kupon masih berlaku.</p>
        </section>

        <section class="glass-panel space-y-5 p-6">
            <header>
                <h2 class="text-lg font-semibold text-white">Estimasi ongkos kirim</h2>
                <p class="text-sm text-white/60">Masukkan tujuan dan pilih kurir favoritmu untuk melihat estimasi biaya.</p>
            </header>
            <div class="grid gap-3 md:grid-cols-3">
                <div class="md:col-span-2">
                    <label class="text-xs uppercase tracking-[0.3em] text-white/40">Kota / Kabupaten</label>
                    <input
                        type="text"
                        class="mt-1 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/40"
                        placeholder="Contoh: Jakarta Selatan"
                        wire:model.defer="shippingForm.destination"
                    >
                </div>
                <div>
                    <label class="text-xs uppercase tracking-[0.3em] text-white/40">Kode Pos</label>
                    <input
                        type="text"
                        class="mt-1 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/40"
                        placeholder="Kode pos"
                        wire:model.defer="shippingForm.postal_code"
                    >
                </div>
                <div>
                    <label class="text-xs uppercase tracking-[0.3em] text-white/40">Kurir (opsional)</label>
                    <select
                        class="mt-1 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/40"
                        wire:model.defer="shippingForm.courier"
                    >
                        <option value="">Semua kurir</option>
                        <option value="jne">JNE</option>
                        <option value="tiki">TIKI</option>
                        <option value="pos">POS Indonesia</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase tracking-[0.3em] text-white/40">Estimasi berat</label>
                    <div class="mt-1 rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white/70">
                        {{ number_format(max(1, $shippingForm['weight'] / 1000), 1) }} kg
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-full border border-white/15 bg-white/10 px-5 py-2 text-sm font-semibold text-white transition hover:bg-white/15 disabled:cursor-not-allowed disabled:opacity-50"
                    wire:click="estimateShipping"
                    wire:loading.attr="disabled"
                    wire:target="estimateShipping"
                >
                    Hitung ongkir
                    <span aria-hidden="true">→</span>
                </button>
                @if ($shippingFeedback)
                    <span class="text-xs text-white/60">{{ $shippingFeedback }}</span>
                @endif
            </div>

            @if (!empty($shippingOptions))
                <div class="divide-y divide-white/10 rounded-2xl border border-white/10 bg-white/[0.03]">
                    @foreach ($shippingOptions as $index => $option)
                        <button
                            type="button"
                            wire:click="chooseShipping({{ $index }})"
                            wire:target="chooseShipping({{ $index }})"
                            wire:loading.attr="disabled"
                            class="w-full text-left transition hover:bg-white/5"
                        >
                            <div class="flex flex-col gap-2 px-4 py-4 text-sm text-white/80 md:flex-row md:items-center md:justify-between">
                                <div class="space-y-1">
                                    <p class="text-base font-semibold text-white">
                                        {{ $option['provider'] }} • {{ $option['service'] }}
                                    </p>
                                    <p class="text-xs text-white/50">{{ $option['description'] }}</p>
                                </div>
                                <div class="text-right text-sm text-white/70">
                                    <p class="text-base font-semibold text-white">Rp {{ number_format($option['cost'], 0, ',', '.') }}</p>
                                    <p class="text-xs text-white/50">{{ $option['etd'] }}</p>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <aside class="space-y-6">
        <section class="glass-panel space-y-5 p-6">
            <header>
                <h2 class="text-lg font-semibold text-white">Ringkasan pembayaran</h2>
                <p class="text-sm text-white/60">Pastikan totalnya sesuai sebelum lanjut ke checkout.</p>
            </header>
            <dl class="space-y-3 text-sm text-white/70">
                <div class="flex items-center justify-between">
                    <dt>Subtotal</dt>
                    <dd class="text-white font-semibold">Rp {{ number_format($cart['totals']['subtotal'], 0, ',', '.') }}</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt>Diskon</dt>
                    <dd class="text-emerald-300 font-semibold">- Rp {{ number_format($cart['totals']['discount'], 0, ',', '.') }}</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt>Ongkir</dt>
                    <dd class="text-white font-semibold">
                        @if ($cart['totals']['shipping'] > 0)
                            Rp {{ number_format($cart['totals']['shipping'], 0, ',', '.') }}
                        @else
                            <span class="text-white/50">Belum ditentukan</span>
                        @endif
                    </dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt>Pajak</dt>
                    <dd class="text-white font-semibold">Rp {{ number_format($cart['totals']['tax'], 0, ',', '.') }}</dd>
                </div>
            </dl>
            <div class="flex items-center justify-between rounded-2xl border border-white/15 bg-white/5 px-4 py-3 text-white">
                <span class="text-sm text-white/60">Total pembayaran</span>
                <span class="text-xl font-semibold">Rp {{ number_format($cart['totals']['grand'], 0, ',', '.') }}</span>
            </div>
            @php($checkoutUrl = \Illuminate\Support\Facades\Route::has('checkout.index') ? route('checkout.index') : '#')
            <a
                href="{{ $checkoutUrl }}"
                class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-[#4de4d4] px-6 py-3 text-sm font-semibold text-neutral-900 transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-40"
                @disabled(empty($cart['items']))
            >
                Lanjut ke checkout
                <span aria-hidden="true">→</span>
            </a>
            @if ($cart['shipping']['service'])
                <p class="text-xs text-white/50">Menggunakan layanan: {{ $cart['shipping']['service'] }}</p>
            @endif
        </section>

        <section class="glass-panel space-y-4 p-6">
            <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-white/40">FAQ Singkat</h3>
            <ul class="space-y-3 text-sm text-white/70">
                <li>
                    <strong class="text-white">Ada batasan maksimum kuantitas?</strong><br>
                    Jika stok varian terbatas, sistem akan memberi tahu ketika melampaui stok tersedia.
                </li>
                <li>
                    <strong class="text-white">Apakah kupon bisa digabung?</strong><br>
                    Untuk saat ini satu keranjang hanya dapat menggunakan satu kupon aktif.
                </li>
                <li>
                    <strong class="text-white">Metode pembayaran apa yang tersedia?</strong><br>
                    Kamu bisa memilih kartu kredit, virtual account, atau e-wallet populer di tahap checkout berikutnya.
                </li>
            </ul>
        </section>
    </aside>
</div>
