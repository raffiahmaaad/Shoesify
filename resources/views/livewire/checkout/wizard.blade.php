@php
    $steps = [
        'address' => ['label' => 'Alamat', 'description' => 'Konfirmasi penerima & alamat pengiriman'],
        'shipping' => ['label' => 'Pengiriman', 'description' => 'Pilih kurir dan estimasi ongkir'],
        'payment' => ['label' => 'Pembayaran', 'description' => 'Metode pembayaran & instruksi'],
        'review' => ['label' => 'Review', 'description' => 'Verifikasi ringkasan order'],
        'complete' => ['label' => 'Selesai', 'description' => 'Order berhasil dibuat'],
    ];
@endphp

<div class="space-y-10">
    <section class="glass-panel overflow-hidden p-6">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-3">
                <div class="flex items-center gap-2 text-xs uppercase tracking-[0.3em] text-white/40">
                    Checkout Progress
                    <span class="inline-flex h-1.5 w-1.5 rounded-full bg-[#4de4d4]"></span>
                    Step {{ array_search($step, array_keys($steps), true) + 1 ?? 1 }} / {{ count($steps) - 1 }}
                </div>
                <h2 class="text-2xl font-semibold text-white">Nyaman & Aman dalam hitungan menit</h2>
                <p class="text-sm text-white/60">
                    Shoesify memandu kamu selangkah demi selangkah. Semua data tersimpan aman dan bisa digunakan ulang di pemesanan berikutnya.
                </p>
            </div>
            <ol class="flex flex-1 flex-col gap-3 text-sm text-white/60 lg:flex-row lg:items-center lg:justify-end">
                @foreach ($steps as $key => $meta)
                    <li class="flex items-center gap-3">
                        <span
                            @class([
                                'flex h-9 w-9 items-center justify-center rounded-full border text-sm font-semibold transition',
                                'border-[#4de4d4]/50 bg-[#4de4d4]/20 text-[#4de4d4]' => $step === $key,
                                'border-white/15 bg-white/5 text-white/50' => $step !== $key,
                            ])
                        >
                            {{ $loop->index + 1 }}
                        </span>
                        <div class="flex flex-col">
                            <span @class(['text-white font-semibold', 'text-white/50' => $step !== $key])>{{ $meta['label'] }}</span>
                            <span class="text-xs text-white/50 hidden md:block">{{ $meta['description'] }}</span>
                        </div>
                    </li>
                    @if (! $loop->last)
                        <svg class="hidden h-6 text-white/20 lg:block" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m8 5 8 7-8 7" />
                        </svg>
                    @endif
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Address Step --}}
    @if ($step === 'address')
        <section class="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)]">
            <div class="glass-panel space-y-6 p-6">
                <header>
                    <h3 class="text-lg font-semibold text-white">Pilih alamat pengiriman</h3>
                    <p class="text-sm text-white/60">Gunakan alamat tersimpan atau tambahkan alamat baru.</p>
                </header>

                @if (!empty($addressBook))
                    <div class="space-y-4">
                        <h4 class="text-xs uppercase tracking-[0.3em] text-white/40">Alamat tersimpan</h4>
                        <div class="space-y-3">
                            @foreach ($addressBook as $address)
                                <label class="flex cursor-pointer items-start gap-3 rounded-3xl border border-white/10 bg-white/5 p-4 text-sm text-white/70 transition hover:bg-white/10" wire:key="address-{{ $address['id'] }}">
                                    <input
                                        type="radio"
                                        class="mt-1"
                                        name="chosen-address"
                                        value="{{ $address['id'] }}"
                                        wire:model="selectedAddressId"
                                    >
                                    <div>
                                        <p class="font-semibold text-white">{{ $address['label'] }} • {{ $address['recipient_name'] }}</p>
                                        <p class="text-xs text-white/50">{{ $address['phone'] }}</p>
                                        <p class="mt-1 text-xs leading-5 text-white/60">
                                            {{ $address['address_line1'] }}{{ $address['address_line2'] ? ', '.$address['address_line2'] : '' }},
                                            {{ $address['city'] }}, {{ $address['province'] }}, {{ $address['postal_code'] }}
                                        </p>
                                        @if ($address['is_default'])
                                            <span class="mt-2 inline-flex items-center gap-2 rounded-full bg-[#4de4d4]/15 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.3em] text-[#4de4d4]">Default</span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-5 py-2 text-sm font-semibold text-white transition hover:bg-white/15"
                    wire:click="useNewAddress"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 5v14m-7-7h14" />
                    </svg>
                    Tambah alamat baru
                </button>

                <div class="space-y-4 rounded-3xl border border-white/10 bg-white/5 p-5">
                    <header class="flex items-center justify-between gap-4">
                        <div>
                            <h4 class="text-sm font-semibold text-white">Form alamat pengiriman</h4>
                            <p class="text-xs text-white/50">Isi jika ingin menggunakan alamat baru.</p>
                        </div>
                        <label class="inline-flex items-center gap-2 text-xs text-white/50">
                            <input type="checkbox" wire:model="addressForm.save_to_book" class="h-4 w-4 rounded border-white/20 bg-transparent text-[#4de4d4] focus:ring-[#4de4d4]/40">
                            Simpan ke buku alamat
                        </label>
                    </header>

                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="text-xs uppercase tracking-[0.3em] text-white/40">Label</label>
                            <input type="text" class="mt-1 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/40" wire:model.defer="addressForm.label" placeholder="Contoh: Rumah / Kantor">
                            @error('addressForm.label') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs uppercase tracking-[0.3em] text-white/40">Nama penerima</label>
                            <input type="text" class="mt-1 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/40" wire:model.defer="addressForm.recipient_name" placeholder="Nama lengkap">
                            @error('addressForm.recipient_name') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs uppercase tracking-[0.3em] text-white/40">No. telepon</label>
                            <input type="text" class="mt-1 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/40" wire:model.defer="addressForm.phone" placeholder="+62">
                            @error('addressForm.phone') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs uppercase tracking-[0.3em] text-white/40">Kota/Kabupaten</label>
                            <input type="text" class="mt-1 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/40" wire:model.defer="addressForm.city" placeholder="Contoh: Jakarta Selatan">
                            @error('addressForm.city') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs uppercase tracking-[0.3em] text-white/40">Provinsi</label>
                            <input type="text" class="mt-1 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/40" wire:model.defer="addressForm.province" placeholder="Contoh: DKI Jakarta">
                            @error('addressForm.province') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs uppercase tracking-[0.3em] text-white/40">Kode pos</label>
                            <input type="text" class="mt-1 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/40" wire:model.defer="addressForm.postal_code" placeholder="Kode pos">
                            @error('addressForm.postal_code') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs uppercase tracking-[0.3em] text-white/40">Alamat detail</label>
                            <textarea class="mt-1 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/40" rows="3" wire:model.defer="addressForm.address_line1" placeholder="Nama jalan, nomor rumah, patokan, dsb."></textarea>
                            @error('addressForm.address_line1') <p class="mt-1 text-xs text-rose-300">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs uppercase tracking-[0.3em] text-white/40">Detail tambahan (opsional)</label>
                            <textarea class="mt-1 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/40" rows="2" wire:model.defer="addressForm.address_line2" placeholder="Apartemen, gedung, lantai, dsb."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="glass-panel space-y-4 p-6">
                <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-white/40">Checklist sebelum lanjut</h3>
                <ul class="space-y-2 text-sm text-white/70">
                    <li>Tulis nama penerima sesuai identitas</li>
                    <li>Pastikan nomor telepon aktif</li>
                    <li>Alamat lengkap memudahkan kurir mengantar</li>
                </ul>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-5 py-2 text-sm font-semibold text-white transition hover:bg-white/15">
                        Kembali ke keranjang
                    </a>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full bg-[#4de4d4] px-6 py-3 text-sm font-semibold text-neutral-900 transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-50"
                        wire:click="proceedAddress"
                        wire:loading.attr="disabled"
                    >
                        Simpan & lanjut
                        <span aria-hidden="true">→</span>
                    </button>
                </div>
            </aside>
        </section>
    @endif

    {{-- Shipping Step --}}
    @if ($step === 'shipping')
        <section class="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)]">
            <div class="glass-panel space-y-6 p-6">
                <header>
                    <h3 class="text-lg font-semibold text-white">Estimasi ongkos kirim</h3>
                    <p class="text-sm text-white/60">Shoesify menghitung estimasi berdasarkan berat keranjang dan tujuan pengiriman.</p>
                </header>

                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="text-xs uppercase tracking-[0.3em] text-white/40">Kota / Kabupaten</label>
                        <input type="text" class="mt-1 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/40" wire:model.defer="shippingForm.destination" />
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.3em] text-white/40">Kode pos</label>
                        <input type="text" class="mt-1 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/40" wire:model.defer="shippingForm.postal_code" />
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.3em] text-white/40">Kurir (opsional)</label>
                        <select class="mt-1 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/40" wire:model.defer="shippingForm.courier">
                            <option value="">Semua kurir</option>
                            <option value="jne">JNE</option>
                            <option value="tiki">TIKI</option>
                            <option value="pos">POS Indonesia</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.3em] text-white/40">Berat total</label>
                        <div class="mt-1 rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white/70">
                            {{ number_format(max(1, $cart['totals']['weight'] ?? 0) / 1000, 1) }} kg
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-5 py-2 text-sm font-semibold text-white transition hover:bg-white/15 disabled:cursor-not-allowed disabled:opacity-50"
                        wire:click="estimateShipping"
                        wire:loading.attr="disabled"
                    >
                        Hitung ongkir
                        <span aria-hidden="true">→</span>
                    </button>
                    @if ($shippingFeedback)
                        <span class="text-xs text-white/60">{{ $shippingFeedback }}</span>
                    @endif
                </div>

                <div class="space-y-3">
                    @forelse ($shippingOptions as $index => $option)
                        <label
                            wire:key="shipping-option-{{ $index }}"
                            class="flex cursor-pointer flex-col gap-2 rounded-3xl border px-4 py-4 transition"
                            @class([
                                'border-[#4de4d4]/60 bg-[#4de4d4]/10 text-white' => $selectedShippingIndex === $index,
                                'border-white/10 bg-white/5 text-white/70 hover:bg-white/10' => $selectedShippingIndex !== $index,
                            ])
                        >
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <input
                                        type="radio"
                                        name="shipping-option"
                                        value="{{ $index }}"
                                        wire:model="selectedShippingIndex"
                                    >
                                    <div>
                                        <p class="text-base font-semibold text-white">
                                            {{ $option['provider'] }} • {{ $option['service'] }}
                                        </p>
                                        <p class="text-xs text-white/60">{{ $option['description'] }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-base font-semibold text-white">Rp {{ number_format($option['cost'], 0, ',', '.') }}</p>
                                    <p class="text-xs text-white/60">{{ $option['etd'] }}</p>
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="rounded-3xl border border-dashed border-white/15 bg-transparent px-4 py-8 text-center text-sm text-white/60">
                            Hitung ongkir terlebih dahulu untuk melihat opsi layanan yang tersedia.
                        </div>
                    @endforelse
                </div>
            </div>

            <aside class="glass-panel space-y-4 p-6">
                <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-white/40">Informasi alamat</h3>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-white/70">
                    <p class="font-semibold text-white">{{ $shippingAddress['recipient_name'] ?? '-' }}</p>
                    <p class="text-xs text-white/50">{{ $shippingAddress['phone'] ?? '-' }}</p>
                    <p class="mt-1 text-xs leading-5 text-white/60">
                        {{ $shippingAddress['address_line1'] ?? '' }} {{ $shippingAddress['address_line2'] ?? '' }}<br>
                        {{ $shippingAddress['city'] ?? '' }}, {{ $shippingAddress['province'] ?? '' }}<br>
                        {{ $shippingAddress['postal_code'] ?? '' }}
                    </p>
                </div>
                <div class="mt-6 flex flex-wrap gap-3">
                    <button type="button" class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-5 py-2 text-sm font-semibold text-white transition hover:bg-white/15" wire:click="backToAddress">
                        ← Ubah alamat
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full bg-[#4de4d4] px-6 py-3 text-sm font-semibold text-neutral-900 transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-50"
                        wire:click="proceedShipping"
                        wire:loading.attr="disabled"
                    >
                        Lanjut ke pembayaran
                        <span aria-hidden="true">→</span>
                    </button>
                </div>
            </aside>
        </section>
    @endif

    {{-- Payment Step --}}
    @if ($step === 'payment')
        <section class="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)]">
            <div class="glass-panel space-y-6 p-6">
                <header>
                    <h3 class="text-lg font-semibold text-white">Metode pembayaran</h3>
                    <p class="text-sm text-white/60">Shoesify terintegrasi dengan Midtrans untuk pembayaran instan, atau pilih transfer manual.</p>
                </header>

                <div class="space-y-4">
                    <label class="flex cursor-pointer flex-col gap-2 rounded-3xl border border-white/10 bg-white/5 p-4 text-sm text-white/70 transition hover:bg-white/10" wire:click="selectPayment('midtrans')">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="payment-method" value="midtrans" wire:model="paymentMethod">
                                <div>
                                    <p class="text-base font-semibold text-white">Midtrans Snap</p>
                                    <p class="text-xs text-white/50">Kartu kredit, virtual account, e-wallet (Gopay, OVO, Shopeepay) dalam satu gateway.</p>
                                </div>
                            </div>
                            <span class="rounded-full bg-[#4de4d4]/20 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.3em] text-[#4de4d4]">Rekomendasi</span>
                        </div>
                        @if ($paymentMethod === 'midtrans' && $paymentMeta)
                            <div class="rounded-2xl border border-[#4de4d4]/40 bg-[#4de4d4]/10 px-4 py-3 text-xs text-[#d1faf0]">
                                Token Snap siap digunakan: <span class="font-mono">{{ $paymentMeta['token'] ?? '' }}</span>. Pembayaran final akan diproses setelah kamu klik <strong>Buat Pesanan</strong>.
                            </div>
                        @endif
                    </label>

                    <label class="flex cursor-pointer flex-col gap-2 rounded-3xl border border-white/10 bg-white/5 p-4 text-sm text-white/70 transition hover:bg-white/10" wire:click="selectPayment('bank_transfer')">
                        <div class="flex items-start gap-3">
                            <input type="radio" name="payment-method" value="bank_transfer" wire:model="paymentMethod">
                            <div>
                                <p class="text-base font-semibold text-white">Transfer manual (Verifikasi 1x24 jam)</p>
                                <p class="text-xs text-white/50">Kami akan mengirim detail rekening & instruksi pembayaran melalui email.</p>
                            </div>
                        </div>
                    </label>

                    <label class="flex cursor-pointer flex-col gap-2 rounded-3xl border border-white/10 bg-white/5 p-4 text-sm text-white/70 transition hover:bg-white/10" wire:click="selectPayment('cod')">
                        <div class="flex items-start gap-3">
                            <input type="radio" name="payment-method" value="cod" wire:model="paymentMethod">
                            <div>
                                <p class="text-base font-semibold text-white">Cash on Delivery (COD)</p>
                                <p class="text-xs text-white/50">Bayar langsung ke kurir, tersedia untuk wilayah Jabodetabek.</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <aside class="glass-panel space-y-4 p-6">
                <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-white/40">Pengingat penting</h3>
                <ul class="space-y-2 text-sm text-white/70">
                    <li>Midtrans Snap menghasilkan token sandbox (uji coba) dan aman untuk development.</li>
                    <li>Transfer manual membutuhkan konfirmasi admin sebelum pengiriman.</li>
                    <li>COD hanya tersedia untuk alamat tertentu dan mungkin dikenakan biaya tambahan.</li>
                </ul>
                <div class="mt-6 flex flex-wrap gap-3">
                    <button type="button" class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-5 py-2 text-sm font-semibold text-white transition hover:bg-white/15" wire:click="backToShipping">
                        ← Kembali ke pengiriman
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full bg-[#4de4d4] px-6 py-3 text-sm font-semibold text-neutral-900 transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-50"
                        wire:click="proceedPayment"
                        wire:loading.attr="disabled"
                    >
                        Review pesanan
                        <span aria-hidden="true">→</span>
                    </button>
                </div>
            </aside>
        </section>
    @endif

    {{-- Review Step --}}
    @if ($step === 'review')
        <section class="grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
            <div class="glass-panel space-y-6 p-6">
                <header>
                    <h3 class="text-lg font-semibold text-white">Ringkasan pesanan</h3>
                    <p class="text-sm text-white/60">Pastikan semua data sudah benar sebelum membuat pesanan.</p>
                </header>
                <div class="divide-y divide-white/10">
                    @foreach ($cart['items'] as $item)
                        <article class="flex flex-col gap-4 py-4 md:flex-row md:items-center md:justify-between">
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
                                    @if ($item['image'])
                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover">
                                    @endif
                                </div>
                                <div class="flex flex-col gap-1 text-sm text-white/70">
                                    <span class="text-base font-semibold text-white">{{ $item['name'] }}</span>
                                    @if ($item['variant_label'])
                                        <span class="text-xs text-white/50">{{ $item['variant_label'] }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-6 text-sm text-white/70">
                                <span>x{{ $item['quantity'] }}</span>
                                <span class="font-semibold text-white">Rp {{ number_format($item['line_total'], 0, ',', '.') }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2 rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-white/70">
                        <p class="text-xs uppercase tracking-[0.3em] text-white/40">Alamat pengiriman</p>
                        <p class="text-white font-semibold">{{ $shippingAddress['recipient_name'] ?? '-' }}</p>
                        <p class="text-xs text-white/50">{{ $shippingAddress['phone'] ?? '-' }}</p>
                        <p class="text-xs leading-5 text-white/60">
                            {{ $shippingAddress['address_line1'] ?? '' }} {{ $shippingAddress['address_line2'] ?? '' }}<br>
                            {{ $shippingAddress['city'] ?? '' }}, {{ $shippingAddress['province'] ?? '' }}<br>
                            {{ $shippingAddress['postal_code'] ?? '' }}
                        </p>
                    </div>
                    <div class="space-y-2 rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-white/70">
                        <p class="text-xs uppercase tracking-[0.3em] text-white/40">Pengiriman & pembayaran</p>
                        <p class="text-white/80">
                            {{ $selectedShipping['provider'] ?? 'Belum dipilih' }} • {{ $selectedShipping['service'] ?? '' }}
                            @if (!empty($selectedShipping)) ({{ $selectedShipping['etd'] }}) @endif
                        </p>
                        <p class="text-xs text-white/50">Biaya ongkir: Rp {{ number_format($selectedShipping['cost'] ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-white/60">Pembayaran: {{ strtoupper(str_replace('_', ' ', $paymentMethod)) }}</p>
                        @if ($paymentMethod === 'midtrans' && $paymentMeta)
                            <p class="text-xs text-white/50">Token: <span class="font-mono">{{ $paymentMeta['token'] }}</span></p>
                         @endif
                    </div>
                </div>
            </div>

            <aside class="space-y-6">
                <section class="glass-panel space-y-5 p-6">
                    <header>
                        <h3 class="text-lg font-semibold text-white">Total akhir</h3>
                        <p class="text-sm text-white/60">Semua harga sudah termasuk PPN dan diskon yang berlaku.</p>
                    </header>
                    <dl class="space-y-3 text-sm text-white/70">
                        <div class="flex items-center justify-between">
                            <dt>Subtotal produk</dt>
                            <dd class="text-white font-semibold">Rp {{ number_format($cart['totals']['subtotal'], 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt>Diskon</dt>
                            <dd class="text-emerald-300 font-semibold">- Rp {{ number_format($cart['totals']['discount'], 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt>Ongkos kirim</dt>
                            <dd class="text-white font-semibold">Rp {{ number_format($cart['totals']['shipping'], 0, ',', '.') }}</dd>
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
                    <label class="flex items-start gap-3 text-xs text-white/60">
                        <input type="checkbox" class="mt-1 h-4 w-4 rounded border-white/20 bg-transparent text-[#4de4d4] focus:ring-[#4de4d4]/40" wire:model="termsAccepted">
                        Saya menyetujui syarat & ketentuan Shoesify terkait pengembalian dan pembayaran.
                    </label>
                    @error('termsAccepted') <p class="text-xs text-rose-300">{{ $message }}</p> @enderror
                    <div class="flex flex-wrap gap-3">
                        <button type="button" class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-5 py-2 text-sm font-semibold text-white transition hover:bg-white/15" wire:click="backToPayment">
                            ← Ubah metode pembayaran
                        </button>
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-full bg-[#4de4d4] px-6 py-3 text-sm font-semibold text-neutral-900 transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-50"
                            wire:click="placeOrder"
                            wire:loading.attr="disabled"
                        >
                            Buat pesanan sekarang
                            <span aria-hidden="true">→</span>
                        </button>
                    </div>
                </section>
            </aside>
        </section>
    @endif

    {{-- Complete Step --}}
    @if ($step === 'complete')
        <section class="glass-panel grid gap-8 px-6 py-12 text-center md:px-10">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border border-[#4de4d4]/50 bg-[#4de4d4]/15 text-[#4de4d4]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </div>
            <div class="space-y-3">
                <h2 class="text-3xl font-semibold text-white">Pesananmu sedang diproses!</h2>
                <p class="text-sm text-white/70">
                    Terima kasih telah berbelanja di Shoesify. Kami sudah mengirim email konfirmasi lengkap beserta detail pembayaran/pengiriman.
                </p>
                <div class="rounded-full border border-white/15 bg-white/10 px-6 py-2 text-sm text-white">
                    Nomor order <span class="font-semibold">{{ $orderNumber }}</span>
                </div>
            </div>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-5 py-2 text-sm font-semibold text-white transition hover:bg-white/15">
                    Kembali ke beranda
                </a>
                @if ($orderId && \Illuminate\Support\Facades\Route::has('orders.show'))
                    <a href="{{ route('orders.show', $orderId) }}" wire:navigate class="inline-flex items-center gap-2 rounded-full bg-[#4de4d4] px-6 py-3 text-sm font-semibold text-neutral-900 transition hover:-translate-y-0.5">
                        Lacak pesanan
                        <span aria-hidden="true">→</span>
                    </a>
                @endif
            </div>
        </section>
    @endif
</div>
