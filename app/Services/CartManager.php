<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Session\Session;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class CartManager
{
    private const DEFAULT_ITEM_WEIGHT_GRAMS = 800;

    public function __construct(
        private readonly Session $session,
    ) {
    }

    public function current(): Cart
    {
        $user = Auth::user();
        $sessionId = $this->ensureSessionId();

        if ($user instanceof Authenticatable) {
            return $this->forUser($user->getAuthIdentifier(), $sessionId);
            // @phpstan-ignore-line Laravel's Authenticatable may return string|int
        }

        return $this->forSession($sessionId);
    }

    public function refreshTotals(Cart $cart): Cart
    {
        $cart->loadMissing([
            'items' => fn ($query) => $query->with(['product', 'variant']),
            'coupon',
        ]);

        $activeItems = $cart->items->where('saved_for_later', false);

        $subtotal = 0;
        $weight = 0;

        /** @var CartItem $item */
        foreach ($activeItems as $item) {
            $unitPrice = $item->unit_price ?? (int) optional($item->product)->price ?? 0;
            $lineTotal = $unitPrice * max($item->quantity, 1);
            $item->forceFill([
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
            ])->save();

            $subtotal += $lineTotal;

            $itemWeight = (int) data_get($item->metadata, 'weight', self::DEFAULT_ITEM_WEIGHT_GRAMS);
            $weight += $itemWeight * $item->quantity;
        }

        $discount = 0;

        if ($cart->coupon) {
            [$valid, $discount] = $this->calculateCouponDiscount($cart->coupon, $cart, $subtotal);

            if (! $valid) {
                $cart->forceFill([
                    'applied_coupon_id' => null,
                ])->save();
                $discount = 0;
            }
        }

        $discount = min($discount, $subtotal);
        $grandTotal = max(($subtotal - $discount) + $cart->shipping_total + $cart->tax_total, 0);

        $cart->forceFill([
            'subtotal' => $subtotal,
            'discount_total' => $discount,
            'weight_total' => $weight,
            'grand_total' => $grandTotal,
        ])->save();

        return $cart->fresh([
            'items' => fn ($query) => $query->with(['product', 'variant']),
            'coupon',
        ]);
    }

    public function updateQuantity(int $itemId, int $quantity): Cart
    {
        $cart = $this->current();
        $item = $this->locateItem($cart, $itemId);

        if ($item->saved_for_later) {
            throw new RuntimeException('Item berada di daftar simpan, tidak bisa diubah kuantitasnya.');
        }

        $quantity = max(1, $quantity);

        $item->update([
            'quantity' => $quantity,
            'line_total' => $item->unit_price * $quantity,
        ]);

        return $this->refreshTotals($cart);
    }

    public function changeQuantity(int $itemId, int $delta): Cart
    {
        $cart = $this->current();
        $item = $this->locateItem($cart, $itemId);

        return $this->updateQuantity($itemId, $item->quantity + $delta);
    }

    public function removeItem(int $itemId): Cart
    {
        $cart = $this->current();
        $item = $this->locateItem($cart, $itemId);
        $item->delete();

        return $this->refreshTotals($cart);
    }

    public function toggleSaveForLater(int $itemId, bool $saved = true): Cart
    {
        $cart = $this->current();
        $item = $this->locateItem($cart, $itemId);

        $item->update([
            'saved_for_later' => $saved,
        ]);

        return $this->refreshTotals($cart);
    }

    public function applyCoupon(string $code): array
    {
        $cart = $this->current();
        $code = Str::upper(trim($code));

        if ($code === '') {
            return [false, 'Kode kupon tidak boleh kosong.'];
        }

        $coupon = Coupon::query()
            ->whereRaw('UPPER(code) = ?', [$code])
            ->active()
            ->first();

        if (! $coupon) {
            return [false, 'Kupon tidak ditemukan atau sudah tidak aktif.'];
        }

        $cart->loadMissing('items');
        $subtotal = $cart->items->where('saved_for_later', false)->sum('line_total');

        if ($subtotal < $coupon->min_subtotal) {
            return [false, 'Minimal belanja untuk kupon ini adalah Rp ' . number_format($coupon->min_subtotal, 0, ',', '.') . '.'];
        }

        if (! $coupon->isWithinUsageLimit()) {
            return [false, 'Kupon telah mencapai batas penggunaan.'];
        }

        $cart->forceFill([
            'applied_coupon_id' => $coupon->id,
        ])->save();

        $cart = $this->refreshTotals($cart);

        return [true, 'Kupon berhasil diterapkan.'];
    }

    public function removeCoupon(): Cart
    {
        $cart = $this->current();

        $cart->forceFill([
            'applied_coupon_id' => null,
            'discount_total' => 0,
        ])->save();

        return $this->refreshTotals($cart);
    }

    public function updateShipping(Cart $cart, string $service, int $cost): Cart
    {
        $cart->forceFill([
            'shipping_service' => $service,
            'shipping_total' => max($cost, 0),
        ])->save();

        return $this->refreshTotals($cart);
    }

    private function forUser(int|string $userId, string $sessionId): Cart
    {
        return DB::transaction(function () use ($userId, $sessionId): Cart {
            $userCart = Cart::query()
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->first();

            if (! $userCart) {
                $userCart = Cart::create([
                    'user_id' => $userId,
                    'status' => 'active',
                ]);
            }

            $guestCart = Cart::query()
                ->where('session_id', $sessionId)
                ->whereNull('user_id')
                ->where('status', 'active')
                ->first();

            if ($guestCart && $guestCart->id !== $userCart->id) {
                $this->mergeCarts($guestCart, $userCart);
                $guestCart->delete();
                $this->session->remove('cart.session_id');
            }

            return $this->refreshTotals($userCart);
        });
    }

    private function forSession(string $sessionId): Cart
    {
        $cart = Cart::query()
            ->where('session_id', $sessionId)
            ->where('status', 'active')
            ->first();

        if (! $cart) {
            $cart = Cart::create([
                'session_id' => $sessionId,
                'status' => 'active',
            ]);
        }

        return $this->refreshTotals($cart);
    }

    private function ensureSessionId(): string
    {
        $sessionId = $this->session->get('cart.session_id');

        if (! $sessionId) {
            $sessionId = Str::uuid()->toString();
            $this->session->put('cart.session_id', $sessionId);
        }

        return $sessionId;
    }

    private function mergeCarts(Cart $source, Cart $target): void
    {
        $source->load('items');
        $target->load('items');

        /** @var Collection<int, CartItem> $targetItems */
        $targetItems = $target->items->keyBy(fn (CartItem $item) => $this->itemIdentity($item));

        foreach ($source->items as $item) {
            $identity = $this->itemIdentity($item);

            if ($targetItems->has($identity)) {
                $existing = $targetItems->get($identity);

                $existing?->update([
                    'quantity' => $existing->quantity + $item->quantity,
                ]);
            } else {
                $target->items()->create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->line_total,
                    'metadata' => $item->metadata,
                    'saved_for_later' => $item->saved_for_later,
                ]);
            }
        }
    }

    private function itemIdentity(CartItem $item): string
    {
        return sprintf('%d:%d', $item->product_id, $item->product_variant_id ?? 0);
    }

    private function locateItem(Cart $cart, int $itemId): CartItem
    {
        $item = $cart->items()->whereKey($itemId)->first();

        if (! $item) {
            throw new RuntimeException('Item keranjang tidak ditemukan.');
        }

        return $item;
    }

    /**
     * @return array{0: bool, 1: int}
     */
    private function calculateCouponDiscount(Coupon $coupon, Cart $cart, int $subtotal): array
    {
        if (! $coupon->is_active) {
            return [false, 0];
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return [false, 0];
        }

        if ($coupon->ends_at && $coupon->ends_at->isPast()) {
            return [false, 0];
        }

        if ($subtotal < $coupon->min_subtotal) {
            return [false, 0];
        }

        if (! $coupon->isWithinUsageLimit()) {
            return [false, 0];
        }

        $discount = 0;

        switch ($coupon->type) {
            case 'fixed':
                $discount = min($coupon->value, $subtotal);
                break;
            case 'percent':
                $discount = (int) round($subtotal * ($coupon->value / 100));
                if ($coupon->max_discount) {
                    $discount = min($discount, $coupon->max_discount);
                }
                break;
            case 'free_shipping':
                $discount = min($cart->shipping_total, $coupon->max_discount ?? $cart->shipping_total);
                break;
            default:
                return [false, 0];
        }

        return [true, $discount];
    }
}
