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
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    Route::prefix('account')->as('account.')->middleware(['verified'])->group(function () {
        Volt::route('account/orders', 'account.orders')->name('orders');
        Volt::route('account/orders/{order}', 'account.orders-show')->name('orders.show');
        Volt::route('account/profile', 'account.profile')->name('profile');
        Volt::route('account/addresses', 'account.addresses')->name('addresses');
        Volt::route('account/wishlist', 'account.wishlist')->name('wishlist');
    });
});
