<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\CartManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class CheckoutProcessor
{
    public function __construct(
        private readonly CartManager $cartManager,
    ) {
    }

    /**
     * @param  array{
     *     shipping_address: array<string, mixed>,
     *     payment_method: string,
     *     payment_payload?: array<string, mixed>|null,
     *     notes?: string|null
     * }  $payload
     */
    public function placeOrder(User $user, int $cartId, array $payload): Order
    {
        return DB::transaction(function () use ($user, $cartId, $payload): Order {
            /** @var Cart $cart */
            $cart = Cart::query()
                ->whereKey($cartId)
                ->where('status', 'active')
                ->with([
                    'items' => fn ($query) => $query->with(['product', 'variant']),
                    'coupon',
                ])
                ->lockForUpdate()
                ->firstOrFail();

            $cart = $this->cartManager->refreshTotals($cart);

            /** @var Collection<int, CartItem> $activeItems */
            $activeItems = $cart->items->where('saved_for_later', false);

            if ($activeItems->isEmpty()) {
                throw new RuntimeException('Keranjang kosong, tidak dapat membuat pesanan.');
            }

            $order = Order::create([
                'user_id' => $user->getKey(),
                'cart_id' => $cart->id,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'subtotal' => $cart->subtotal,
                'discount_total' => $cart->discount_total,
                'shipping_total' => $cart->shipping_total,
                'shipping_service' => $cart->shipping_service,
                'tax_total' => $cart->tax_total,
                'grand_total' => $cart->grand_total,
                'payment_method' => $payload['payment_method'],
                'payment_status' => 'pending',
                'payment_payload' => $payload['payment_payload'] ?? null,
                'shipping_address' => $payload['shipping_address'],
                'billing_address' => $payload['shipping_address'],
                'notes' => $payload['notes'] ?? null,
                'placed_at' => now(),
            ]);

            foreach ($activeItems as $item) {
                $snapshot = [
                    'product' => [
                        'id' => $item->product?->id,
                        'name' => $item->product?->name,
                        'slug' => $item->product?->slug,
                        'sku' => $item->product?->sku,
                        'brand' => $item->product?->brand?->name,
                        'image' => collect($item->product?->images ?? [])->first(),
                    ],
                    'variant' => [
                        'id' => $item->variant?->id,
                        'size' => $item->variant?->size,
                        'color_name' => $item->variant?->color_name,
                        'color_hex' => $item->variant?->color_hex,
                        'sku' => $item->variant?->sku,
                    ],
                ];

                $order->items()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price,
                    'subtotal' => $item->line_total,
                    'product_snapshot' => $snapshot,
                ]);
            }

            if ($cart->coupon) {
                $cart->coupon->increment('usage_count');
            }

            $cart->forceFill([
                'status' => 'submitted',
            ])->save();

            return $order->fresh(['items']);
        });
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}
