<footer class="border-t border-white/5 bg-neutral-950/85 py-14 backdrop-blur-xl">
    <div class="mx-auto grid w-full max-w-7xl gap-10 px-6 md:grid-cols-2 lg:grid-cols-4 md:px-10">
        <div class="space-y-5">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-semibold tracking-tight text-white">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white/10 text-lg text-white">S</span>
                Shoesify
            </a>
            <p class="text-sm text-white/60">
                Crafted in Jakarta dengan visi global sneakerheads. Kami menghubungkan koleksi terbatas, pengalaman checkout yang mulus, dan layanan concierge dalam satu platform.
            </p>
            <div class="flex items-center gap-3 text-white/60">
                <a href="#" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 transition hover:bg-white/15 hover:text-white">
                    <span class="sr-only">Instagram</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5Zm10 2H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3Zm-5 4.5A4.5 4.5 0 1 1 7.5 13 4.5 4.5 0 0 1 12 8.5Zm0 2A2.5 2.5 0 1 0 14.5 13 2.5 2.5 0 0 0 12 10.5Zm5.25-3a.75.75 0 1 1-.75.75.75.75 0 0 1 .75-.75Z"/></svg>
                </a>
                <a href="#" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 transition hover:bg-white/15 hover:text-white">
                    <span class="sr-only">TikTok</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M16.5 3a4.5 4.5 0 0 0 4 4.472V10a9 9 0 1 1-10-8.944V5.85a4.5 4.5 0 1 0 4.5 4.5V3Z"/></svg>
                </a>
                <a href="#" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 transition hover:bg-white/15 hover:text-white">
                    <span class="sr-only">YouTube</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M21.6 7.2a2.7 2.7 0 0 0-1.9-2C18 4.8 12 4.8 12 4.8s-6 0-7.7.4a2.7 2.7 0 0 0-1.9 2A28 28 0 0 0 2 12a28 28 0 0 0 .3 4.8 2.7 2.7 0 0 0 1.9 2c1.7.4 7.7.4 7.7.4s6 0 7.7-.4a2.7 2.7 0 0 0 1.9-2A28 28 0 0 0 22 12a28 28 0 0 0-.4-4.8ZM10 15.15V8.85l5.2 3.15Z"/></svg>
                </a>
            </div>
        </div>

        <div x-data="footerSection({ defaultOpen: true })" class="space-y-3">
            <button type="button" class="flex w-full items-center justify-between text-left text-sm font-semibold text-white/70 md:cursor-default" x-on:click="toggle()" x-bind:aria-expanded="open">
                <span>Customer Service</span>
                <svg class="h-5 w-5 text-white/40 md:hidden" x-bind:class="open ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m6 9 6 6 6-6" />
                </svg>
            </button>
            <ul x-show="open" x-transition class="space-y-2 text-sm text-white/50" style="display: none;">
                <li><a href="#" class="transition hover:text-white">Pusat Bantuan</a></li>
                <li><a href="#" class="transition hover:text-white">Pelacakan Pesanan</a></li>
                <li><a href="#" class="transition hover:text-white">Pengembalian & Penukaran</a></li>
                <li><a href="#" class="transition hover:text-white">Ketentuan Garansi</a></li>
            </ul>
        </div>

        <div x-data="footerSection({ defaultOpen: true })" class="space-y-3">
            <button type="button" class="flex w-full items-center justify-between text-left text-sm font-semibold text-white/70 md:cursor-default" x-on:click="toggle()" x-bind:aria-expanded="open">
                <span>Perusahaan</span>
                <svg class="h-5 w-5 text-white/40 md:hidden" x-bind:class="open ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m6 9 6 6 6-6" />
                </svg>
            </button>
            <ul x-show="open" x-transition class="space-y-2 text-sm text-white/50" style="display: none;">
                <li><a href="#" class="transition hover:text-white">Tentang Shoesify</a></li>
                <li><a href="#" class="transition hover:text-white">Karier</a></li>
                <li><a href="#" class="transition hover:text-white">Kolaborasi & Media</a></li>
                <li><a href="#" class="transition hover:text-white">Kebijakan Privasi</a></li>
            </ul>
        </div>

        <div x-data="footerSection({ defaultOpen: true })" class="space-y-3">
            <button type="button" class="flex w-full items-center justify-between text-left text-sm font-semibold text-white/70 md:cursor-default" x-on:click="toggle()" x-bind:aria-expanded="open">
                <span>Newsletter</span>
                <svg class="h-5 w-5 text-white/40 md:hidden" x-bind:class="open ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m6 9 6 6 6-6" />
                </svg>
            </button>
            <div x-show="open" x-transition class="space-y-3" style="display: none;">
                <p class="text-sm text-white/50">Dapatkan kurasi eksklusif, pengingat restock, dan akses early-bird langsung di inbox.</p>
                <form class="space-y-3" data-newsletter>
                    <label class="sr-only" for="footer-email">Email address</label>
                    <input
                        id="footer-email"
                        type="email"
                        required
                        placeholder="nama@email.com"
                        class="w-full rounded-full border border-white/15 bg-white/10 px-5 py-3 text-sm text-white placeholder:text-white/40 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/70"
                    />
                    <button type="submit" class="w-full rounded-full bg-[#4de4d4] px-5 py-3 text-sm font-semibold text-neutral-900 shadow-[0_18px_45px_rgba(77,228,212,0.45)] transition hover:-translate-y-0.5">
                        Bergabung
                    </button>
                </form>
                <p class="text-xs text-white/40">Dengan mendaftar, kamu menyetujui kebijakan privasi kami. Bisa berhenti kapan saja.</p>
            </div>
        </div>
    </div>

    <div class="mx-auto mt-12 flex w-full max-w-7xl flex-col gap-3 px-6 text-xs text-white/40 md:flex-row md:items-center md:justify-between md:px-10">
        <p>© {{ now()->year }} Shoesify. All rights reserved.</p>
        <div class="flex flex-wrap gap-4">
            <a href="#" class="transition hover:text-white/70">Syarat & Ketentuan</a>
            <a href="#" class="transition hover:text-white/70">Kebijakan Privasi</a>
            <a href="#" class="transition hover:text-white/70">Cookie</a>
        </div>
    </div>
</footer>
