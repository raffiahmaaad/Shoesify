    <div class="fixed inset-0 z-[60] hidden items-center justify-center bg-neutral-950/80 px-6 backdrop-blur-md" role="dialog" aria-modal="true" data-modal>
        <div class="relative w-full max-w-xl overflow-hidden rounded-[28px] border border-white/10 bg-neutral-950/90 shadow-[0_30px_80px_rgba(5,15,33,0.65)]">
            <button type="button" class="absolute right-5 top-5 flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-white/10 text-white transition hover:bg-white/20" data-modal-close>
                <span class="sr-only">Close quick view</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="grid gap-6 overflow-hidden md:grid-cols-[0.8fr_1fr]" data-modal-content>
                <img src="" alt="" class="h-full w-full object-cover" data-modal-image>
                <div class="flex flex-col gap-4 p-6 text-white/80">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-[#4de4d4]">Quick View</p>
                        <h3 class="mt-2 text-2xl font-semibold text-white" data-modal-title></h3>
                    </div>
                    <p class="text-sm leading-relaxed text-white/70" data-modal-description></p>
                    <div class="flex items-center gap-3 text-sm text-white/50">
                        <span class="flex items-center gap-1 text-[#f8c572]" data-modal-rating></span>
                        <span>•</span>
                        <span data-modal-reviews></span>
                    </div>
                    <div class="space-y-3 rounded-2xl border border-white/10 bg-white/5 p-5">
                        <div class="flex items-baseline gap-3">
                            <span class="text-2xl font-semibold text-white" data-modal-price></span>
                            <span class="text-sm text-white/40 line-through" data-modal-original-price></span>
                        </div>
                        <div class="flex gap-2" data-modal-colors></div>
                    </div>
                    <div class="mt-auto flex flex-wrap gap-3">
                        <button type="button" class="rounded-full bg-[#4de4d4] px-6 py-3 text-sm font-semibold text-neutral-900 shadow-[0_25px_60px_rgba(77,228,212,0.45)] transition hover:-translate-y-0.5" data-add-to-cart>
                            Add to cart
                        </button>
                        <button type="button" class="rounded-full border border-white/15 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10" data-wishlist>
                            Add to wishlist
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
