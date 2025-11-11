<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('/products', 'products.index')->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::view('/cart', 'cart.index')->name('cart.index');
Route::view('/checkout', 'checkout.index')
    ->middleware(['auth', 'verified'])
    ->name('checkout.index');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Redirect legacy /settings routes to /account/settings
    Route::redirect('settings', 'account/settings');

    // All account-related routes (profile, orders, addresses, wishlist, settings)
    Route::prefix('account')->as('account.')->middleware(['verified'])->group(function () {
        // Profile & Orders
        Volt::route('profile', 'account.profile')->name('profile');
        Volt::route('orders', 'account.orders')->name('orders');
        Volt::route('orders/{order}', 'account.orders-show')->name('orders.show');

        // Addresses & Wishlist
        Volt::route('addresses', 'account.addresses')->name('addresses');
        Volt::route('wishlist', 'account.wishlist')->name('wishlist');

        // Settings (moved under /account)
        Volt::route('settings', 'account.settings')->name('settings');
        Volt::route('settings/password', 'account.settings-password')->name('settings.password');
        Volt::route('settings/appearance', 'account.settings-appearance')->name('settings.appearance');
        Volt::route('settings/two-factor', 'account.settings-two-factor')
            ->middleware(
                when(
                    Features::canManageTwoFactorAuthentication()
                        && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                    ['password.confirm'],
                    [],
                ),
            )
            ->name('settings.two-factor');
    });
});

Route::get('/products/category/{category}', [ProductController::class, 'index'])->name('products.category');
Route::get('/products/brand/{brand}', [ProductController::class, 'byBrand'])->name('products.brand');
