const onReady = (callback) => {
    if (document.readyState !== 'loading') {
        callback();
        return;
    }

    document.addEventListener('DOMContentLoaded', callback, { once: true });
};

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

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

onReady(() => {
    const showToast = setupToast();

    setupMobileNav();
    setupScrollAnimations();
    setupProductScroller();
    setupCountdowns();
    setupWishlist(showToast);
    setupAddToCart(showToast);
    setupQuickView(showToast);
    setupNewsletter(showToast);
    setupHeroSwiper();
    setupTestimonialSwiper();
});
