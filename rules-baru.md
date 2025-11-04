⚠️ CRITICAL DEVELOPMENT RULES - BACA INI DULU

🎯 ZERO-ERROR POLICY

SETIAP FILE YANG KAMU BUAT HARUS:

✅ 100% BEBAS ERROR - Tidak ada syntax error, logic error, atau runtime error

✅ TESTED & VERIFIED - Setiap function/method harus di-test sebelum dianggap selesai

✅ VALIDATED - Semua input harus tervalidasi dengan ketat

✅ ERROR HANDLED - Setiap kemungkinan error harus di-handle dengan graceful

✅ TYPE SAFE - Gunakan type hints di PHP, proper typing di JavaScript

✅ DOCUMENTED - Code yang complex harus punya inline comments

✅ CONSISTENT - Follow coding standards & naming conventions tanpa exception

KONTEKS PROYEK

Kamu adalah AI Developer Expert yang akan membangun aplikasi e-commerce sepatu modern bernama Shoesify menggunakan Laravel 12 + Livewire 3 + Flux + Volt + Folio + Filament 4.0 + TailwindCSS 4.0.

Proyek sudah memiliki struktur Laravel lengkap dengan autentikasi Livewire StarterKit. JANGAN INSTALL ULANG atau RE-INIT Laravel!

🎯 OBJECTIVE UTAMA

Bangun aplikasi e-commerce sepatu yang:

Ultra Modern & Interaktif - Seperti Shopee/Tokopedia/Zalora

Smooth Animations - Setiap interaksi terasa premium

Mobile-First Responsive - Perfect di semua device

High Performance - Fast load, optimized images, lazy loading

User Experience Excellence - Intuitive, engaging, addictive

🛠️ TECH STACK SPECIFICATIONS

Backend

Laravel 12 (PHP 8.4)

Livewire 3 - Real-time interactions tanpa page reload

Volt - Single-file Livewire components

Folio - File-based routing

Filament 4.0 - Admin panel modern

MySQL 8 - Database

Frontend

TailwindCSS 4.0 - Utility-first styling

Flux Components - Pre-built UI components

Alpine.js - Lightweight JS framework

Flowbite - UI component library

Swiper.js - Touch slider untuk product carousel

Design System

Primary Color: #016B61 (Teal accent)

Dark Base: #1A2A4F (Navy blue)

Typography: Inter/Poppins font family

Spacing: 4px base grid system

Shadows: Multi-layer depth shadows

Animations: 200-300ms transitions, easing functions

🎨 DESIGN REQUIREMENTS - DETAILED

0. Global UI Elements (Header & Footer)

HEADER (STICKY):
┌─────────────────────────────────────────────────────────────────---------------─┐
│ [Logo] [Produk] [Collections] [Categories] [Promotion] [Search Bar...] [Cart] [👤] [☀️/🌙] │
└──────────────────────────────────────────────────────────────────---------------┘

FEATURES:

-   Logo: Link ke Homepage
-   Categories:
-   Search Bar:
    -   Full-width di mobile (click icon)
    -   Auto-suggestion (Livewire)
    -   Menampilkan 3 produk teratas + link "Lihat Semua"
    -   Recent searches (localStorage)
-   Cart Icon: Badge dinamis (Livewire), buka mini-cart on hover
-   User Icon (BARU):
    -   Tampilkan [Login/Register] jika guest
    -   Tampilkan Avatar/Inisial jika logged in
    -   On click, buka dropdown menu:
        -   Halo, [Nama User]
        -   Akun Saya (Link ke User Dashboard)
        -   Pesanan Saya
        -   Wishlist
        -   Notifikasi (Bell icon dengan badge)
        ***
        -   Logout (action)
-   Dark/Light Mode Toggle (BARU):
    -   Tombol icon (Sun/Moon)
    -   Menggunakan Alpine.js + localStorage
    -   Menambahkan/menghapus class 'dark' dari <html>
    -   Transisi warna yang smooth (transition-colors duration-300)

FOOTER:

-   4-column layout (About, Customer Service, Social Media, Newsletter Signup)
-   Collapsible accordion di mobile
-   Social media icons dengan hover effect

1. Homepage Design

HERO SECTION:

-   Full-width auto-play banner carousel (Swiper.js)
-   Min 3 slides dengan CTA buttons
-   Smooth fade transitions + parallax effect
-   Overlay gradient untuk readability
-   Responsive text scaling

CATEGORY CARDS:

-   Grid: 4 kolom (desktop) → 2 kolom (mobile)
-   Hover effect: Scale 1.05 + shadow elevation
-   Icon/image + category name
-   Smooth color transitions

FEATURED PRODUCTS:

-   Horizontal scrollable grid (snap scroll)
-   Product card dengan:
    -   Image zoom on hover
    -   Wishlist heart icon (top-right, animated)
    -   Quick view button on hover
    -   Price + discount badge
    -   Add to cart button dengan ripple effect
    -   Rating stars + review count

PROMOTIONAL BANNERS:

-   2-column layout dengan parallax scrolling
-   Animated on viewport entry (fade + slide up)

TESTIMONIALS:

-   Card carousel dengan auto-advance
-   Avatar + name + rating + review text
-   Smooth infinite loop

2. Product Listing Page

LAYOUT:
┌─────────────────────────────────────┐
│ Breadcrumb Navigation │
├──────────┬──────────────────────────┤
│ │ │
│ FILTER │ PRODUCT GRID │
│ SIDEBAR │ (Masonry/Grid) │
│ │ │
│ • Price │ [Card] [Card] [Card] │
│ • Brand │ [Card] [Card] [Card] │
│ • Size │ [Card] [Card] [Card] │
│ • Color │ │
│ │ [Load More Button] │
└──────────┴──────────────────────────┘

FEATURES:

-   Sticky filter sidebar (desktop)
-   Collapsible filter accordion (mobile)
-   Real-time filter dengan Livewire
-   Sort dropdown: (Popular, Price, Newest)
-   View toggle: Grid/List view
-   Infinite scroll OR Load More pagination
-   Skeleton loading states
-   Empty state illustration jika no results

3. Product Detail Page

LAYOUT (2-Column):
┌──────────────────┬──────────────────┐
│ IMAGE GALLERY │ PRODUCT INFO │
│ ┌────────────┐ │ │
│ │ Main Image │ │ Brand Logo │
│ │ (Zoomable)│ │ Product Name │
│ └────────────┘ │ ★★★★☆ (120) │
│ │ │
│ [▣][▣][▣][▣] │ Rp 1.299.000 │
│ Thumbnails │ -20% discount │
│ │ │
│ │ Size Selector │
│ │ [38][39][40]... │
│ │ │
│ │ Color Picker │
│ │ ●●●●● │
│ │ │
│ │ Quantity: [- 1 +]│
│ │ │
│ │ [Add to Cart] ♥ │
│ │ [Buy Now] │
└──────────────────┴──────────────────┘

TABS BELOW:
┌─────────────────────────────────────┐
│ [Description] [Specs] [Reviews] │
├─────────────────────────────────────┤
│ Tab content dengan smooth fade │
└─────────────────────────────────────┘

FEATURES:

-   Image gallery dengan:

    -   Click to zoom (lightbox modal)
    -   Swipe gestures (mobile)
    -   Thumbnail navigation
    -   360° view (optional)

-   Variant selector dengan:

    -   Visual feedback (border highlight)
    -   Disabled state untuk out of stock
    -   Stock indicator per variant

-   Sticky "Add to Cart" bar (mobile scroll)
-   Share buttons (WhatsApp, FB, Twitter, Copy Link)
-   Recently viewed products section
-   Related products carousel

4. Shopping Cart

LAYOUT:
┌─────────────────────────────────────┐
│ Shopping Cart (3 items) │
├──────────────────────┬──────────────┤
│ CART ITEMS LIST │ SUMMARY │
│ │ │
│ ┌─────────────────┐│ Subtotal │
│ │ [Img] Product ││ Shipping │
│ │ Size: 42 ││ Tax │
│ │ [- 1 +] [×] ││ ───────── │
│ │ Rp 899.000 ││ Total │
│ └─────────────────┘│ Rp X.XXX │
│ │ │
│ [Item 2] │ [Checkout] │
│ [Item 3] │ │
└──────────────────────┴──────────────┘

INTERACTIONS:

-   Real-time quantity update (Livewire)
-   Remove item dengan confirm dialog (custom modal, NO alert())
-   Apply coupon code dengan validation
-   Toast notification untuk setiap action
-   Empty cart illustration + CTA
-   Suggested products sidebar
-   Save for later feature

5. Checkout Flow

MULTI-STEP PROCESS:
Step 1: Shipping Address

-   Autocomplete address
-   Save multiple addresses
-   Set default address

Step 2: Shipping Method

-   Radio cards dengan pricing
-   Estimated delivery time
-   Tracking info

Step 3: Payment Method

-   Payment gateway cards
-   Saved payment methods
-   Secure badge indicators

Step 4: Review Order

-   Full order summary
-   Edit capabilities
-   Terms & conditions checkbox

Step 5: Confirmation

-   Success animation (Lottie)
-   Order number + tracking
-   Download invoice button
-   Continue shopping CTA

PROGRESS INDICATOR:
┌─────────────────────────────────────┐
│ ●────●────●────○────○ │
│ Ship Pay Review Confirm │
└─────────────────────────────────────┘

6. User Dashboard / "My Account" Page (BARU)

LAYOUT (2-Column):
┌──────────────────┬──────────────────┐
│ SIDE NAVIGATION │ PAGE CONTENT │
│ │ │
│ ┌────────────┐ │ [Content Area] │
│ │ [Avatar] │ │ │
│ │ Nama User │ │ │
│ └────────────┘ │ │
│ │ │
│ • Profile │ │
│ • Alamat │ │
│ • Pesanan Saya │ │
│ • Wishlist │ │
│ • Notifikasi │ │
│ • Pengaturan │ │
│ • Logout │ │
└──────────────────┴──────────────────┘

FEATURES:

-   Layout Penuh menggunakan Livewire dengan navigasi (`wire:navigate`)
-   Profile: Edit nama, email, no. telp, tgl lahir, ganti password
-   Alamat: CRUD untuk multiple shipping addresses
-   Pesanan Saya:
    -   List semua pesanan (dengan filter status)
    -   Klik untuk lihat detail pesanan
    -   Lacak pengiriman, konfirmasi penerimaan
    -   Beri ulasan
-   Wishlist: Grid produk yang di-wishlist, "Add to Cart"
-   Notifikasi: List update pesanan, promo, dll
-   Pengaturan: Termasuk Dark Mode preference, Email notifications

🔥 ADVANCED INTERACTIONS & ANIMATIONS

Micro-interactions

Button Hover States

-   Scale: 1.02
-   Shadow elevation increase
-   Color brightness +10%
-   Smooth 200ms transition

Product Card Hover

-   Image zoom: scale(1.1)
-   Slide-up info overlay
-   Quick action buttons fade in
-   Shadow: 0 20px 40px rgba(0,0,0,0.15)

Add to Cart Animation

-   Button shake effect
-   Cart icon badge bounce
-   Product thumbnail flies to cart
-   Success toast notification

Wishlist Heart

-   Click: Heart fill animation (scale + color)
-   Particle burst effect
-   Haptic feedback (mobile)

Quantity Selector

-   Disable button jika min/max reached
-   Number change dengan slide animation
-   Debounced update (500ms)

Loading States

<!-- Skeleton Screens -->
<div class="animate-pulse">
    <div class="h-64 bg-gray-200 rounded"></div>
    <div class="h-4 bg-gray-200 rounded mt-4"></div>
    <div class="h-4 bg-gray-200 rounded mt-2 w-2/3"></div>
</div>

<!-- Spinner untuk actions -->
<svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
    <!-- spinner path -->
</svg>

🔥 ADVANCED FEATURES (Shopee/Tokopedia Level) (BARU)

1. Flash Sale System

Komponen homepage dengan countdown timer

Dedicated page untuk semua flash sale

Logic di products table (atau pivot table) untuk flash_sale_start, flash_sale_end, flash_sale_price

Logic untuk handle stock yang dialokasikan untuk flash sale

Visual "Stock Terjual %" bar

2. Voucher & Promotion System

vouchers table (code, type, value, min_purchase, usage_limit, valid_until)

Logic untuk apply voucher di cart/checkout

Menampilkan voucher yang tersedia di halaman produk atau cart

Auto-apply best voucher

3. Social Login

Integrasi dengan Laravel Socialite

Tombol "Login with Google", "Login with Facebook" di auth pages

Logic untuk link social account ke user (atau create new user)

4. Notification Center

notifications table (user_id, type, data, read_at)

Bell icon di header dengan unread count (Livewire polling)

Dropdown/page untuk melihat semua notifikasi (Order status, promo, review replies)

🗄️ DATABASE SCHEMA

-- Sudah ada di migrations, pastikan lengkap:

users

-   id, name, email, password, role, avatar
-   phone, birth_date, gender
-   email_verified_at, remember_token
-   timestamps

categories

-   id, name, slug, description, image
-   parent_id (self-referencing)
-   is_active, sort_order
-   timestamps

brands

-   id, name, slug, logo, description
-   is_featured, is_active
-   timestamps

products

-   id, category_id, brand_id
-   name, slug, sku
-   description, short_description
-   price, compare_price, cost_price
-   is_active, is_featured
-   images (JSON array)
-   meta_title, meta_description
-   timestamps, soft_deletes

product_variants

-   id, product_id
-   size, color, sku
-   stock_quantity, price_adjustment
-   images (JSON)
-   timestamps

carts

-   id, user_id, session_id
-   timestamps

cart_items

-   id, cart_id, product_id, variant_id
-   quantity, price
-   timestamps

orders

-   id, user_id, order_number
-   status (pending, processing, shipped, delivered, cancelled)
-   subtotal, shipping_cost, tax, discount, total
-   payment_method, payment_status
-   shipping_address (JSON)
-   notes, tracking_number
-   timestamps

order_items

-   id, order_id, product_id, variant_id
-   quantity, price, subtotal
-   product_snapshot (JSON)
-   timestamps

addresses

-   id, user_id
-   label (Home, Office, etc)
-   name, phone, address_line1, address_line2
-   city, province, postal_code, country
-   is_default
-   timestamps

reviews

-   id, product_id, user_id, order_id
-   rating, title, content
-   images (JSON)
-   is_verified_purchase
-   helpful_count
-   timestamps

wishlists

-   id, user_id, product_id
-   timestamps

-- (Optional - for Advanced Features)
vouchers

-   id, code, description
-   type (percentage, fixed)
-   value, min_purchase
-   max_uses, uses_count
-   starts_at, expires_at
-   is_active
-   timestamps

notifications

-   id, user_id
-   type (order_update, promo, etc)
-   data (JSON)
-   read_at
-   timestamps

🏛️ ARSITEKTUR & ALUR DATA (BARU)

Single Source of Truth (SSOT)

Database adalah SSOT. Semua data (produk, kategori, brand, order) HANYA ada di database MySQL.

Filament adalah "Kepala" (Head). Ini adalah satu-satunya interface admin untuk CRUD (Create, Read, Update, Delete) semua data. Apa yang admin input di Filament (mis: menambah produk baru, set is_featured=true) adalah data yang SAH.

Frontend (Livewire/Volt) adalah "Badan" (Body). Frontend HANYA membaca data dari database yang sama.

Konsistensi Data (Permintaan Anda): Klasifikasi produk, brand, dan kategori di frontend (mis: di filter sidebar, di halaman kategori) HARUS 100% MENCERMINKAN data yang ada di database, yang dikelola melalui Filament.

CategoryResource di Filament mengelola tabel categories.

Komponen FilterSidebar.php di frontend membaca dari tabel categories.

Jika admin menambah/menghapus/mengubah urutan kategori di Filament, frontend HARUS otomatis menampilkan perubahan tersebut (via query database yang baru).

Tidak ada data duplikat. Frontend tidak boleh memiliki logic bisnis atau data yang terpisah dari apa yang dikelola oleh admin panel.

📱 RESPONSIVE BREAKPOINTS

// TailwindCSS 4.0 Breakpoints
{
'sm': '640px', // Mobile landscape
'md': '768px', // Tablet
'lg': '1024px', // Desktop
'xl': '1280px', // Large desktop
'2xl': '1536px' // Extra large
}

// Layout Behavior:
Mobile (< 640px):

-   Single column
-   Bottom navigation (optional, tapi modern)
-   Collapsible filters
-   Stack all elements

Tablet (640px - 1024px):

-   2-column grid
-   Sidebar filters
-   Larger touch targets

Desktop (> 1024px):

-   3-4 column grid
-   Sticky header
-   Hover interactions
-   Side-by-side layouts

🎭 COMPONENT LIBRARY (Flux + Custom)

Reusable Components

// ProductCard.php (Volt Component)

<div class="product-card group">
  <div class="image-wrapper">
    <img src="{{ $product->image }}"
         alt="{{ $product->name }}"
         class="group-hover:scale-110 transition-transform">
    <button wire:click="toggleWishlist" class="wishlist-btn">
      <x-icon name="heart" />
    </button>
  </div>
  <div class="product-info">
    <h3>{{ $product->name }}</h3>
    <div class="price">
      <span class="current">{{ $product->price }}</span>
      @if($product->compare_price)
        <span class="old">{{ $product->compare_price }}</span>
      @endif
    </div>
    <button wire:click="addToCart" class="btn-primary">
      Add to Cart
    </button>
  </div>
</div>

// FilterSidebar.php
// CategoryNav.php
// ProductGallery.php
// QuantitySelector.php
// PriceRange.php
// ReviewCard.php
// CheckoutSteps.php
// UserMenuDropdown.php (BARU)

🔐 FILAMENT ADMIN PANEL

Dashboard Widgets

// StatCards

-   Total Orders (with trend)
-   Revenue Today/Month
-   Active Products
-   Pending Reviews

// Charts

-   Sales Line Chart (Chart.js)
-   Top Products Bar Chart
-   Category Distribution Pie Chart
-   Traffic Sources

// Recent Orders Table

-   Live updates dengan polling
-   Quick action buttons
-   Status badges

Resources

ProductResource

-   Form dengan image upload multiple
-   Variant repeater
-   Rich text editor untuk description
-   SEO fields
-   Bulk actions (activate, delete)
-   Import/Export CSV

OrderResource

-   Timeline status tracking
-   Print invoice action
-   Send notification action
-   Refund management
-   Customer notes

CategoryResource

-   Tree view untuk hierarchy
-   Drag & drop reordering
-   Image upload

BrandResource

-   Logo upload
-   Featured toggle

UserResource

-   Role management
-   Order history
-   Activity log

ReviewResource

-   Approve/reject moderation
-   Reply to reviews
-   Flag inappropriate content

⚡ PERFORMANCE OPTIMIZATION

// 1. Image Optimization

-   Use WebP format
-   Lazy loading: loading="lazy"
-   Responsive images: srcset
-   CDN integration (optional)

// 2. Database Optimization

-   Eager loading relationships
-   Database indexing
-   Query caching (Redis)
    DB::table('products')
    ->with(['category', 'brand', 'variants'])
    ->cacheFor(3600)
    ->get();

// 3. Livewire Optimization

-   Lazy loading components
-   Defer loading: wire:init
-   Polling optimization
-   Wire:key untuk lists
-   `wire:navigate` untuk navigasi SPA-like

// 4. Asset Optimization

-   Vite bundling
-   CSS purging (TailwindCSS)
-   JS minification
-   Code splitting

// 5. Caching Strategy

-   Route caching
-   Config caching
-   View caching
-   Query result caching

🧪 TESTING REQUIREMENTS

// Feature Tests (Pest)
test('user can add product to cart', function() {
$user = User::factory()->create();
$product = Product::factory()->create();

$this->actingAs($user)
->post('/cart/add', ['product_id' => $product->id])
->assertStatus(200);

expect(Cart::where('user_id', $user->id)->count())->toBe(1);
});

test('checkout flow completes successfully');
test('product search returns relevant results');
test('filter products by category and price');
test('admin can create product with variants');
test('user can update profile in dashboard'); // BARU
test('dark mode toggle works'); // BARU

// Browser Tests (Laravel Dusk - optional)

-   Test complete purchase flow
-   Test mobile responsiveness
-   Test animations and interactions

📋 IMPLEMENTATION CHECKLIST

Phase 1: Foundation (Week 1)

[ ] Setup database tables & seeders

[ ] Create base Livewire/Volt components

[ ] Implement auth flow (already done)

[ ] Setup Filament admin panel

[ ] Create product & category seeders dengan data realistis

Phase 2: Frontend (Week 2)

[ ] Build Global Header & Footer (termasuk User Icon & Dark Mode)

[ ] Build homepage dengan semua sections

[ ] Product listing page dengan filters

[ ] Product detail page dengan gallery

[ ] Shopping cart functionality

[ ] Implement semua animations

Phase 3: Checkout & User Account (Week 3)

[ ] Multi-step checkout flow

[ ] Payment gateway integration (Midtrans)

[ ] Order management system

[ ] Build User Dashboard (Profile, Alamat, Pesanan)

[ ] Email notifications

[ ] Invoice generation

Phase 4: Admin & Polish (Week 4)

[ ] Complete Filament resources

[ ] Dashboard widgets & analytics

[ ] Implement Advanced Features (Flash Sale, Vouchers)

[ ] Optimize performance

[ ] Testing & bug fixes

[ ] Deployment preparation

💡 BEST PRACTICES & TIPS

Code Organization

-   Gunakan Volt untuk single-file components
-   Service classes untuk business logic
-   Repository pattern untuk data access
-   Traits untuk reusable functionality

Naming Conventions

-   Components: PascalCase (ProductCard.php)
-   Methods: camelCase (addToCart)
-   Variables: snake_case (product_id)
-   Routes: kebab-case (/product-detail)

Security

-   CSRF protection (Laravel default)
-   Input validation di setiap form
-   SQL injection prevention (Eloquent)
-   XSS protection (Blade escaping)
-   Rate limiting untuk API & forms

UX Excellence

-   Loading states untuk semua async actions
-   Error messages yang helpful
-   Success feedback yang clear
-   Progressive disclosure (show advanced options on demand)
-   Keyboard shortcuts untuk power users

🚨 CRITICAL REMINDERS

❌ JANGAN:

Install ulang Laravel atau dependencies yang sudah ada

Ubah struktur folder core Laravel

Hapus migrations yang sudah ada

Hardcode credentials atau API keys

Skip validasi input

Ignore error handling

Gunakan alert() atau confirm() (buat custom modal)

✅ LAKUKAN:

Build incrementally (component by component)

Test setiap feature sebelum move on

Commit frequently dengan descriptive messages

Document complex logic dengan comments

Follow Laravel & TailwindCSS best practices

Prioritize user experience di setiap decision

🎯 SUCCESS CRITERIA

Project dianggap sukses jika:

✅ Homepage load < 2 detik

✅ Semua interactions smooth (no lag)

✅ Mobile responsive sempurna

✅ Complete purchase flow works end-to-end

✅ Admin panel fully functional

✅ Animations enhance UX (not distract)

✅ Zero console errors

✅ Clean, maintainable code

✅ User feedback: "Wow, this feels premium!"

📞 QUICK REFERENCE

# Development

npm run dev # Start Vite
php artisan serve # Start Laravel server
php artisan migrate:fresh --seed # Reset DB dengan fresh data

# Livewire

php artisan make:volt ComponentName
php artisan livewire:publish --config
php artisan livewire:publish --assets

# Filament

php artisan make:filament-resource Product
php artisan make:filament-widget LatestOrders

# Testing

php artisan test
php artisan test --filter=ProductTest

# Optimization

php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

🚀 NOW START BUILDING! Fokus pada delivering exceptional user experience dengan code yang clean dan maintainable. Every pixel matters, every interaction counts!
