const onReady = (callback) => {
    if (document.readyState !== 'loading') {
        callback();
        return;
    }

    document.addEventListener('DOMContentLoaded', callback, { once: true });
};

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const fluxAppearanceStorageKey = 'flux.appearance';
const recentSearchStorageKey = 'shoesify:recent-searches';

const ensureFluxThemeBridge = () => {
    try {
        const prefersDarkMedia = window.matchMedia('(prefers-color-scheme: dark)');

        const readStoredPreference = () => {
            const stored = localStorage.getItem(fluxAppearanceStorageKey);
            return stored === 'dark' || stored === 'light' ? stored : null;
        };

        const resolveInitialMode = () => {
            const stored = readStoredPreference();
            if (stored) {
                return stored;
            }

            return prefersDarkMedia.matches ? 'dark' : 'light';
        };

        let appearanceTarget = typeof window.$flux === 'object' && window.$flux !== null ? window.$flux : {};
        let currentMode = resolveInitialMode();

        const applyMode = (mode, { persistChoice = true } = {}) => {
            const nextMode = mode === 'system' ? (prefersDarkMedia.matches ? 'dark' : 'light') : mode;
            currentMode = nextMode === 'dark' ? 'dark' : 'light';

            if (window.Flux?.applyAppearance) {
                window.Flux.applyAppearance(mode);
            } else {
                document.documentElement.classList.toggle('dark', currentMode === 'dark');
            }

            document.documentElement.dataset.theme = currentMode;
            window.dispatchEvent(new CustomEvent('flux:appearance-changed', { detail: { mode: currentMode } }));

            if (persistChoice) {
                if (mode === 'system') {
                    localStorage.removeItem(fluxAppearanceStorageKey);
                } else {
                    localStorage.setItem(fluxAppearanceStorageKey, currentMode);
                }
            } else if (mode === 'system') {
                localStorage.removeItem(fluxAppearanceStorageKey);
            }
        };

        applyMode(currentMode, { persistChoice: false });

        const proxyHandler = {
            get(_unused, property) {
                if (property === 'dark') {
                    return currentMode === 'dark';
                }

                const value = Reflect.get(appearanceTarget, property);
                return typeof value === 'function' ? value.bind(appearanceTarget) : value;
            },
            set(_unused, property, value) {
                if (property === 'dark') {
                    applyMode(Boolean(value) ? 'dark' : 'light');
                    return true;
                }

                if (property === 'appearance') {
                    if (value === 'system') {
                        applyMode('system', { persistChoice: false });
                    } else if (value === 'dark' || value === true) {
                        applyMode('dark');
                    } else if (value === 'light' || value === false) {
                        applyMode('light');
                    } else {
                        Reflect.set(appearanceTarget, property, value);
                    }

                    return true;
                }

                return Reflect.set(appearanceTarget, property, value);
            },
            has(_unused, property) {
                if (property === 'dark') {
                    return true;
                }

                return property in appearanceTarget;
            },
        };

        const proxy = new Proxy({}, proxyHandler);

        Object.defineProperty(window, '$flux', {
            configurable: true,
            enumerable: true,
            get() {
                return proxy;
            },
            set(value) {
                if (value && typeof value === 'object') {
                    appearanceTarget = value;
                    if ('dark' in value) {
                        applyMode(Boolean(value.dark) ? 'dark' : 'light');
                    } else if ('appearance' in value && typeof value.appearance === 'string') {
                        applyMode(value.appearance, { persistChoice: false });
                    }
                }
            },
        });

        prefersDarkMedia.addEventListener('change', (event) => {
            if (readStoredPreference() === null) {
                applyMode(event.matches ? 'dark' : 'light', { persistChoice: false });
            }
        });
    } catch (error) {
        console.warn('Unable to initialize Flux theme bridge', error);
    }
};

onReady(() => ensureFluxThemeBridge());

window.headerShell = () => ({
    mobileNav: false,
    mobileSearch: false,
    init() {
        ensureFluxThemeBridge();
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024 && this.mobileNav) {
                this.mobileNav = false;
                document.documentElement.classList.remove('overflow-hidden');
            }
        });
    },
    toggleSearch() {
        this.mobileSearch = !this.mobileSearch;

        if (this.mobileSearch) {
            window.setTimeout(() => {
                const input = document.querySelector('[data-mobile-search] input[type="search"]');
                input?.focus();
            }, 120);
        }
    },
    toggleMobileNav() {
        this.mobileNav = !this.mobileNav;
        document.documentElement.classList.toggle('overflow-hidden', this.mobileNav);
    },
    closeMobileNav() {
        if (this.mobileNav) {
            this.mobileNav = false;
            document.documentElement.classList.remove('overflow-hidden');
        }
    },
});

window.navSections = (homePath = '/') => ({
    currentSection: null,
    homePath,
    activeClass:
        'glass-chip hover:-translate-y-0.5',
    inactiveClass:
        'text-white/70 hover:-translate-y-0.5 hover:bg-white/10 hover:text-white dark:text-zinc-400 dark:hover:bg-zinc-800/80 dark:hover:text-zinc-100',
    init() {
        this.homePath = homePath || '/';
        this.syncFromLocation();
        window.addEventListener('hashchange', () => this.syncFromLocation());
        window.addEventListener('popstate', () => this.syncFromLocation());
    },
    syncFromLocation() {
        const onHome = window.location.pathname === this.homePath;
        this.currentSection = onHome ? (window.location.hash.replace('#', '') || null) : null;
    },
    setSection(section) {
        if (!section) {
            return;
        }
        this.currentSection = section;
    },
    classes(section) {
        if (!section) {
            return this.inactiveClass;
        }

        return this.currentSection === section ? `${this.activeClass}` : this.inactiveClass;
    },
});

window.headerSearchStore = () => ({
    recent: [],
    dropdown: false,
    init() {
        this.recent = this.read();
        this.$watch('$wire.query', (value) => {
            this.dropdown = Boolean(value);
        });
    },
    open() {
        this.dropdown = true;
    },
    close() {
        this.dropdown = false;
    },
    isOpen(query) {
        return this.dropdown || Boolean(query) || this.recent.length > 0;
    },
    remember(value) {
        const term = (value ?? '').trim();
        if (!term) {
            return;
        }

        this.recent = [
            term,
            ...this.recent.filter((item) => item.toLowerCase() !== term.toLowerCase()),
        ].slice(0, 6);

        this.persist();
    },
    apply(value) {
        this.remember(value);
        this.$wire.set('query', value);
        this.$nextTick(() => this.$refs.input?.focus());
        this.dropdown = true;
    },
    clear() {
        this.recent = [];
        this.persist();
    },
    read() {
        try {
            const stored = localStorage.getItem(recentSearchStorageKey);
            const parsed = stored ? JSON.parse(stored) : [];
            return Array.isArray(parsed) ? parsed : [];
        } catch (error) {
            console.warn('Failed to parse stored searches', error);
            return [];
        }
    },
    persist() {
        localStorage.setItem(recentSearchStorageKey, JSON.stringify(this.recent));
    },
});

window.darkToggle = () => ({
    dark: document.documentElement.classList.contains('dark'),
    init() {
        window.addEventListener('flux:appearance-changed', (event) => {
            this.dark = event.detail.mode === 'dark';
        });
    },
    toggle() {
        window.$flux.dark = !this.dark;
    },
});

window.footerSection = ({ defaultOpen = false } = {}) => ({
    open: defaultOpen,
    init() {
        const media = window.matchMedia('(min-width: 768px)');
        const sync = () => {
            this.open = media.matches ? true : defaultOpen;
        };

        sync();
        media.addEventListener('change', sync);
    },
    toggle() {
        if (window.innerWidth >= 768) {
            return;
        }

        this.open = !this.open;
    },
});

const loadSwiper = (() => {
    let loader;

    return () => {
        if (!loader) {
            loader = import('https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.esm.browser.min.js');
        }

        return loader;
    };
})();

const setupToast = () => {
    const container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);

    return (message, options = {}) => {
        const { duration = 3200 } = options;
        if (!message) {
            return;
        }

        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;
        container.appendChild(toast);

        window.setTimeout(() => {
            toast.classList.add('is-hiding');
            window.setTimeout(() => {
                toast.remove();
            }, 320);
        }, duration);
    };
};

const setupMobileNav = () => {
    const toggle = document.querySelector('[data-mobile-toggle]');
    const menu = document.querySelector('[data-mobile-menu]');

    if (!toggle || !menu) {
        return;
    }

    const close = () => {
        toggle.setAttribute('aria-expanded', 'false');
        menu.classList.add('hidden');
    };

    toggle.addEventListener('click', () => {
        const isOpen = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', String(!isOpen));
        menu.classList.toggle('hidden', isOpen);
    });

    menu.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', close);
    });
};

const setupScrollAnimations = () => {
    const elements = document.querySelectorAll('[data-animate]');
    if (!elements.length) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.25 }
    );

    elements.forEach((element) => observer.observe(element));
};

const setupProductScroller = () => {
    document.querySelectorAll('[data-product-track]').forEach((track) => {
        const container = track.closest('section');
        if (!container) {
            return;
        }

        const prev = container.querySelector('[data-scroll-left]');
        const next = container.querySelector('[data-scroll-right]');

        const scrollByAmount = (direction) => {
            track.scrollBy({
                left: direction * Math.max(track.clientWidth * 0.7, 320),
                behavior: 'smooth',
            });
        };

        prev?.addEventListener('click', () => scrollByAmount(-1));
        next?.addEventListener('click', () => scrollByAmount(1));
    });
};

const setupCountdowns = () => {
    const formatTime = (diffMs) => {
        const seconds = Math.max(0, Math.floor(diffMs / 1000));
        const days = Math.floor(seconds / 86400);
        const hours = Math.floor((seconds % 86400) / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        return `${days}d ${hours}h ${minutes}m ${secs}s`;
    };

    document.querySelectorAll('[data-countdown]').forEach((element) => {
        const targetValue = element.getAttribute('data-countdown-target');
        const targetDate = targetValue ? new Date(targetValue) : null;

        if (!targetDate || Number.isNaN(targetDate.getTime())) {
            element.textContent = 'soon';
            return;
        }

        const update = () => {
            const diff = targetDate.getTime() - Date.now();
            element.textContent = diff <= 0 ? 'ended' : formatTime(diff);
        };

        update();
        const interval = window.setInterval(update, 1000);
        element.addEventListener('destroy', () => window.clearInterval(interval), { once: true });
    });
};

const setupWishlist = (showToast) => {
    document.querySelectorAll('[data-wishlist]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            button.classList.toggle('is-active');
            const isActive = button.classList.contains('is-active');
            showToast(isActive ? 'Produk tersimpan di wishlist.' : 'Produk dihapus dari wishlist.');
        });
    });
};

const createRipple = (button, event) => {
    const rect = button.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const ripple = document.createElement('span');
    ripple.className = 'ripple';
    ripple.style.width = ripple.style.height = `${size}px`;

    const clientX = event.clientX || rect.left + rect.width / 2;
    const clientY = event.clientY || rect.top + rect.height / 2;

    ripple.style.left = `${clientX - rect.left - size / 2}px`;
    ripple.style.top = `${clientY - rect.top - size / 2}px`;

    button.appendChild(ripple);
    ripple.addEventListener('animationend', () => ripple.remove());
};

const setupAddToCart = (showToast) => {
    document.querySelectorAll('[data-add-to-cart]').forEach((button) => {
        button.addEventListener('click', (event) => {
            createRipple(button, event);
            showToast('Produk berhasil masuk ke keranjang.');
            window.dispatchEvent(new CustomEvent('cart-updated'));
        });
    });
};

const setupBuyNow = (showToast) => {
    document.querySelectorAll('[data-buy-now], [data-sticky-cta]').forEach((button) => {
        button.addEventListener('click', (event) => {
            createRipple(button, event);
            showToast('Checkout ekspres segera tersedia.');
        });
    });
};

const setupQuickView = (showToast) => {
    const modal = document.querySelector('[data-modal]');
    if (!modal) {
        return;
    }

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    };

    const populateModal = (product) => {
        const image = modal.querySelector('[data-modal-image]');
        const title = modal.querySelector('[data-modal-title]');
        const description = modal.querySelector('[data-modal-description]');
        const rating = modal.querySelector('[data-modal-rating]');
        const reviews = modal.querySelector('[data-modal-reviews]');
        const price = modal.querySelector('[data-modal-price]');
        const originalPrice = modal.querySelector('[data-modal-original-price]');
        const colors = modal.querySelector('[data-modal-colors]');

        if (image) {
            image.src = product.image ?? '';
            image.alt = product.name ?? 'Selected product';
        }

        if (title) {
            title.textContent = product.name ?? 'Unknown product';
        }

        if (description) {
            description.textContent = product.description ?? '';
        }

        if (reviews) {
            reviews.textContent = `${product.reviews ?? 0} ulasan`;
        }

        if (price) {
            price.textContent = product.price ? `$${product.price}` : '';
        }

        if (originalPrice) {
            const original = product.discount
                ? Math.round(product.price / (1 - product.discount / 100))
                : '';
            originalPrice.textContent = original ? `$${original}` : '';
            originalPrice.classList.toggle('invisible', !original);
        }

        if (rating) {
            rating.innerHTML = '';
            const rounded = Math.round(product.rating ?? 0);
            for (let index = 0; index < rounded; index += 1) {
                const star = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                star.setAttribute('viewBox', '0 0 24 24');
                star.setAttribute('class', 'h-4 w-4 fill-current');
                star.innerHTML =
                    '<path d="m12 2.5 2.263 6.483h6.612l-5.352 3.891 2.263 6.483L12 15.466l-5.786 3.791 2.263-6.483-5.352-3.891h6.612L12 2.5z" />';
                rating.appendChild(star);
            }
        }

        if (colors) {
            colors.innerHTML = '';
            (product.colors ?? []).forEach((tone) => {
                const swatch = document.createElement('span');
                swatch.className =
                    'h-8 w-8 rounded-full border border-white/20 shadow-[0_6px_16px_rgba(0,0,0,0.2)]';
                swatch.style.backgroundColor = tone;
                colors.appendChild(swatch);
            });
        }
    };

    modal.querySelector('[data-modal-close]')?.addEventListener('click', closeModal);
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.querySelectorAll('[data-quick-view]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const productCard = trigger.closest('[data-product-card]');
            if (!productCard) {
                return;
            }

            try {
                const product = JSON.parse(productCard.getAttribute('data-product') ?? '{}');
                populateModal(product);
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            } catch (error) {
                console.error('Failed to open quick view', error);
                showToast('Maaf, terjadi kesalahan menampilkan produk.');
            }
        });
    });
};

const setupNewsletter = (showToast) => {
    const form = document.querySelector('[data-newsletter]');
    if (!form) {
        return;
    }

    const emailInput = form.querySelector('input[type="email"]');
    if (!emailInput) {
        return;
    }

    const emailRegex =
        /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z]{2,})+$/;

    const clearState = () => {
        emailInput.classList.remove('input-error');
        emailInput.removeAttribute('aria-invalid');
    };

    emailInput.addEventListener('input', clearState);

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const value = emailInput.value.trim();

        if (!emailRegex.test(value)) {
            emailInput.classList.add('input-error');
            emailInput.setAttribute('aria-invalid', 'true');
            showToast('Alamat email belum valid, coba lagi ya.');
            return;
        }

        clearState();
        form.reset();
        showToast('Terima kasih! Kami akan mengirim kurasi terbaik ke inbox kamu.');
    });
};

const setupHeroSwiper = () => {
    const heroInstance = document.querySelector('[data-hero-swiper]');
    if (!heroInstance) {
        return;
    }

    loadSwiper()
        .then((module) => {
            const Swiper = module.Swiper ?? module.default;
            const { Autoplay, EffectFade, Navigation, Pagination, Parallax } = module;

            if (!Swiper) {
                return;
            }

            const navigation = {
                nextEl: heroInstance.querySelector('[data-hero-next]'),
                prevEl: heroInstance.querySelector('[data-hero-prev]'),
            };

            const pagination = {
                el: heroInstance.querySelector('[data-hero-pagination]'),
                clickable: true,
            };

            new Swiper(heroInstance, {
                modules: [Autoplay, EffectFade, Navigation, Pagination, Parallax],
                effect: 'fade',
                parallax: true,
                loop: true,
                speed: 900,
                allowTouchMove: true,
                navigation,
                pagination,
                autoplay: prefersReducedMotion.matches
                    ? false
                    : {
                          delay: 5200,
                          disableOnInteraction: false,
                      },
            });
        })
        .catch((error) => {
            console.error('Failed to initialise hero swiper', error);
        });
};

const setupTestimonialSwiper = () => {
    const testimonialSwiper = document.querySelector('[data-testimonial-swiper]');
    if (!testimonialSwiper) {
        return;
    }

    loadSwiper()
        .then((module) => {
            const Swiper = module.Swiper ?? module.default;
            const { Autoplay, Navigation, Pagination } = module;

            if (!Swiper) {
                return;
            }

            new Swiper(testimonialSwiper, {
                modules: [Autoplay, Navigation, Pagination],
                slidesPerView: 1.1,
                spaceBetween: 24,
                loop: true,
                centeredSlides: false,
                autoplay: prefersReducedMotion.matches
                    ? false
                    : {
                          delay: 4800,
                          disableOnInteraction: false,
                      },
                pagination: {
                    el: testimonialSwiper.querySelector('[data-testimonial-pagination]'),
                    clickable: true,
                },
                navigation: {
                    nextEl: testimonialSwiper.querySelector('[data-testimonial-next]'),
                    prevEl: testimonialSwiper.querySelector('[data-testimonial-prev]'),
                },
                breakpoints: {
                    768: { slidesPerView: 2, spaceBetween: 28 },
                    1280: { slidesPerView: 2.5, spaceBetween: 32 },
                },
            });
        })
        .catch((error) => {
            console.error('Failed to initialise testimonial swiper', error);
        });
};

const setupProductDetail = (showToast) => {
    const container = document.querySelector('[data-product-detail]');
    if (!container) {
        return;
    }

    const payload = (() => {
        try {
            return JSON.parse(container.getAttribute('data-product') ?? '{}');
        } catch (error) {
            console.error('Failed to parse product payload', error);
            return {};
        }
    })();

    const state = {
        color: payload.colors?.[0]?.name ?? null,
        size: payload.sizes?.[0] ?? null,
        quantity: 1,
    };

    const mainImage = container.querySelector('[data-product-main-image]');
    const priceEl = container.querySelector('[data-product-price]');
    const originalPriceEl = container.querySelector('[data-product-original-price]');
    const stockEl = container.querySelector('[data-product-stock]');
    const sticky = document.querySelector('[data-product-sticky]');
    const stickyPrice = document.querySelector('[data-sticky-price]');

    const findVariant = () =>
        (payload.variants ?? []).find(
            (variant) =>
                (!state.color || variant.color === state.color) &&
                (!state.size || variant.size === state.size)
        );

    const availableStock = () => {
        const variant = findVariant();
        if (variant) {
            return Math.max(0, Number.parseInt(variant.stock ?? 0, 10));
        }

        return (payload.variants ?? []).reduce(
            (carry, variant) => carry + Math.max(0, Number.parseInt(variant.stock ?? 0, 10)),
            0
        );
    };

    const computePrice = () => {
        const basePrice = Number.parseInt(payload.price ?? 0, 10);
        const variant = findVariant();
        const adjusted = variant ? basePrice + Number.parseInt(variant.price_adjustment ?? 0, 10) : basePrice;
        const price = Math.max(0, adjusted);

        const original = (() => {
            if (variant && payload.originalPrice) {
                return payload.originalPrice + Number.parseInt(variant.price_adjustment ?? 0, 10);
            }

            return payload.originalPrice ?? null;
        })();

        return { price, original };
    };

    const formatCurrency = (amount) => {
        return `$${Number.parseInt(amount, 10).toLocaleString('en-US')}`;
    };

    const markActiveButtons = () => {
        container.querySelectorAll('[data-variant-color]').forEach((button) => {
            const value = button.getAttribute('data-variant-color');
            const isActive = value === state.color;
            button.classList.toggle('is-active', isActive);
        });

        container.querySelectorAll('[data-variant-size]').forEach((button) => {
            const size = button.getAttribute('data-variant-size');
            const variantExists = (payload.variants ?? []).some((variant) => {
                const matchesSize = variant.size === size;
                const matchesColor = !state.color || variant.color === state.color;
                return matchesSize && matchesColor && Number.parseInt(variant.stock ?? 0, 10) > 0;
            });

            const isDisabled = !variantExists;
            button.dataset.disabled = String(isDisabled);
            button.toggleAttribute('disabled', isDisabled);

            if (isDisabled && state.size === size) {
                state.size = null;
            }

            const isActive = size === state.size;
            button.classList.toggle('bg-white', isActive);
            button.classList.toggle('text-neutral-900', isActive);
            button.classList.toggle('border-white', isActive);
            button.classList.toggle('text-white/70', !isActive);
            button.classList.toggle('border-white/15', !isActive);
        });
    };

    const updateQuantityControls = () => {
        const quantityInput = container.querySelector('[data-quantity-input]');
        const decrease = container.querySelector('[data-quantity-decrease]');
        const increase = container.querySelector('[data-quantity-increase]');
        const max = availableStock();

        if (!quantityInput || !decrease || !increase) {
            return;
        }

        quantityInput.value = String(state.quantity);
        decrease.disabled = state.quantity <= 1;
        increase.disabled = state.quantity >= max;

        decrease.classList.toggle('opacity-40', decrease.disabled);
        increase.classList.toggle('opacity-40', increase.disabled);
    };

    const updatePrice = () => {
        const { price, original } = computePrice();
        const stock = availableStock();

        if (priceEl) {
            priceEl.textContent = formatCurrency(price);
        }

        if (stickyPrice) {
            stickyPrice.textContent = formatCurrency(price * state.quantity);
        }

        if (originalPriceEl) {
            if (original && original > price) {
                originalPriceEl.textContent = formatCurrency(original);
                originalPriceEl.classList.remove('invisible');
            } else {
                originalPriceEl.textContent = '';
                originalPriceEl.classList.add('invisible');
            }
        }

        if (stockEl) {
            stockEl.textContent = `${stock} pasang`;
        }

        const addToCart = container.querySelector('[data-add-to-cart]');
        const stickyCta = document.querySelector('[data-sticky-cta]');
        const disabled = stock <= 0;

        [addToCart, stickyCta].forEach((button) => {
            if (!button) {
                return;
            }
            button.toggleAttribute('disabled', disabled);
            button.classList.toggle('opacity-60', disabled);
        });
    };

    const refreshMainImage = () => {
        const variant = findVariant();
        const candidateImage =
            (variant?.images && variant.images.length ? variant.images[0] : null) ??
            (payload.images && payload.images.length ? payload.images[0] : null);

        if (candidateImage && mainImage) {
            mainImage.src = candidateImage;
        }
    };

    const syncState = () => {
        if (!state.size && payload.sizes && payload.sizes.length > 0) {
            state.size = payload.sizes.find((size) => {
                return (payload.variants ?? []).some((variant) => {
                    const matchesSize = variant.size === size;
                    const matchesColor = !state.color || variant.color === state.color;
                    return matchesSize && matchesColor && Number.parseInt(variant.stock ?? 0, 10) > 0;
                });
            }) ?? payload.sizes[0];
        }

        if (!state.color && payload.colors && payload.colors.length > 0) {
            state.color = payload.colors[0].name;
        }

        state.quantity = Math.max(1, Math.min(state.quantity, availableStock() || 1));

        markActiveButtons();
        updateQuantityControls();
        updatePrice();
        refreshMainImage();
    };

    container.querySelectorAll('[data-variant-color]').forEach((button) => {
        button.addEventListener('click', () => {
            state.color = button.getAttribute('data-variant-color');
            syncState();
        });
    });

    container.querySelectorAll('[data-variant-size]').forEach((button) => {
        button.addEventListener('click', () => {
            if (button.dataset.disabled === 'true') {
                return;
            }

            state.size = button.getAttribute('data-variant-size');
            syncState();
        });
    });

    const quantityInput = container.querySelector('[data-quantity-input]');
    const decrease = container.querySelector('[data-quantity-decrease]');
    const increase = container.querySelector('[data-quantity-increase]');

    if (quantityInput) {
        quantityInput.addEventListener('change', () => {
            const value = Number.parseInt(quantityInput.value, 10);
            if (!Number.isNaN(value)) {
                state.quantity = Math.max(1, Math.min(value, availableStock() || 1));
                syncState();
            }
        });
    }

    decrease?.addEventListener('click', () => {
        state.quantity = Math.max(1, state.quantity - 1);
        syncState();
    });

    increase?.addEventListener('click', () => {
        state.quantity = Math.min(availableStock() || 1, state.quantity + 1);
        syncState();
    });

    const thumbnails = container.querySelectorAll('[data-product-thumbnail]');
    thumbnails.forEach((thumbnail) => {
        thumbnail.addEventListener('click', () => {
            const image = thumbnail.getAttribute('data-image');
            if (image && mainImage) {
                mainImage.src = image;
            }
        });
    });

    const zoomButton = container.querySelector('[data-product-zoom]');
    const lightbox = document.querySelector('[data-product-lightbox]');
    const lightboxImage = document.querySelector('[data-product-lightbox-image]');
    const lightboxClose = document.querySelector('[data-product-lightbox-close]');

    const openLightbox = (image) => {
        if (!lightbox || !lightboxImage) {
            return;
        }

        lightboxImage.src = image;
        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    };

    const closeLightbox = () => {
        if (!lightbox) {
            return;
        }

        lightbox.classList.add('hidden');
        lightbox.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    };

    zoomButton?.addEventListener('click', () => {
        if (mainImage?.src) {
            openLightbox(mainImage.src);
        }
    });

    lightboxClose?.addEventListener('click', closeLightbox);
    lightbox?.addEventListener('click', (event) => {
        if (event.target === lightbox) {
            closeLightbox();
        }
    });

    container.querySelectorAll('[data-tab-trigger]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const id = trigger.getAttribute('data-tab-trigger');

            container.querySelectorAll('[data-tab-trigger]').forEach((button) => {
                button.classList.toggle('bg-white/15', button === trigger);
                button.classList.toggle('text-white', button === trigger);
            });

            container.querySelectorAll('[data-tab-panel]').forEach((panel) => {
                panel.classList.toggle('hidden', panel.getAttribute('data-tab-panel') !== id);
            });
        });
    });

    const shareButtons = container.querySelectorAll('[data-share]');
    shareButtons.forEach((button) => {
        button.addEventListener('click', async () => {
            const type = button.getAttribute('data-share');
            const url = window.location.href;
            const message = `Lihat ${payload.name} di Shoesify: ${url}`;

            if (type === 'copy') {
                try {
                    await navigator.clipboard.writeText(url);
                    showToast('Tautan berhasil disalin.');
                } catch (error) {
                    console.error('Copy failed', error);
                    showToast('Gagal menyalin tautan. Coba lagi.');
                }
            }

            if (type === 'whatsapp') {
                const encoded = encodeURIComponent(message);
                window.open(`https://wa.me/?text=${encoded}`, '_blank', 'noopener');
            }
        });
    });

    window.addEventListener('cart-updated', () => {
        // Placeholder to hook future inventory updates.
    });

    syncState();

    if (sticky) {
        const setVisibility = () => {
            sticky.classList.toggle('opacity-0', window.scrollY < 360);
        };
        setVisibility();
        document.addEventListener('scroll', setVisibility, { passive: true });
    }
};

onReady(() => {
    const showToast = setupToast();

    setupMobileNav();
    setupScrollAnimations();
    setupProductScroller();
    setupCountdowns();
    setupWishlist(showToast);
    setupAddToCart(showToast);
    setupBuyNow(showToast);
    setupQuickView(showToast);
    setupNewsletter(showToast);
    setupHeroSwiper();
    setupTestimonialSwiper();
    setupProductDetail(showToast);
});
