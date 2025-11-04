<?php

declare(strict_types=1);

namespace App\Livewire\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Services\CartManager;
use App\Services\Shipping\ShippingEstimator;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class Page extends Component
{
    public array $cartState = [];
    public string $couponCode = '';
    public ?string $flashMessage = null;

    /** @var array{destination: string, postal_code: string, courier: ?string, weight: int} */
    public array $shippingForm = [
        'destination' => '',
        'postal_code' => '',
        'courier' => null,
        'weight' => 0,
    ];

    /** @var array<int, array{provider: string, service: string, etd: string, cost: int, description: string}> */
    public array $shippingOptions = [];

    public ?string $shippingFeedback = null;

    private CartManager $cartManager;
    private ShippingEstimator $shippingEstimator;

    public function boot(CartManager $cartManager, ShippingEstimator $shippingEstimator): void
    {
        $this->cartManager = $cartManager;
        $this->shippingEstimator = $shippingEstimator;
    }

    public function mount(): void
    {
        $this->hydrateCart();
    }

    #[On('cart-updated')]
    public function hydrateCart(): void
    {
        $cart = $this->cartManager->current();
        $cart->loadMissing([
            'items' => fn ($query) => $query->with(['product', 'variant']),
            'coupon',
        ]);

        $this->cartState = $this->presentCart($cart);

        // Reset shipping form weight-based placeholder
        $this->shippingForm['weight'] = $cart->weight_total ?? 0;
    }

    public function incrementQuantity(int $itemId): void
    {
        $this->cartManager->changeQuantity($itemId, +1);
        $this->afterCartMutation('Jumlah produk berhasil ditambah.');
    }

    public function decrementQuantity(int $itemId): void
    {
        $this->cartManager->changeQuantity($itemId, -1);
        $this->afterCartMutation('Jumlah produk berhasil dikurangi.');
    }

    public function updateQuantity(int $itemId, $quantity): void
    {
        if (! is_numeric($quantity)) {
            return;
        }

        $this->cartManager->updateQuantity($itemId, (int) $quantity);
        $this->afterCartMutation('Jumlah produk diperbarui.');
    }

    public function removeItem(int $itemId): void
    {
        $this->cartManager->removeItem($itemId);
        $this->afterCartMutation('Produk dihapus dari keranjang.');
    }

    public function saveForLater(int $itemId): void
    {
        $this->cartManager->toggleSaveForLater($itemId, true);
        $this->afterCartMutation('Produk dipindahkan ke Simpan Nanti.');
    }

    public function moveToCart(int $itemId): void
    {
        $this->cartManager->toggleSaveForLater($itemId, false);
        $this->afterCartMutation('Produk dikembalikan ke keranjang.');
    }

    public function applyCoupon(): void
    {
        [$success, $message] = $this->cartManager->applyCoupon($this->couponCode);
        $this->flashMessage = $message;

        if ($success) {
            $this->couponCode = '';
            $this->dispatch('cart-updated');
        }
    }

    public function removeCoupon(): void
    {
        $this->cartManager->removeCoupon();
        $this->afterCartMutation('Kupon berhasil dihapus.');
    }

    public function estimateShipping(): void
    {
        $cart = $this->cartManager->current();

        $weight = max(1, $cart->weight_total ?: 0);

        $payload = [
            'destination' => $this->shippingForm['destination'],
            'postal_code' => $this->shippingForm['postal_code'],
            'courier' => $this->shippingForm['courier'],
            'weight' => $weight,
        ];

        $this->shippingOptions = $this->shippingEstimator->estimate($payload);

        if (empty($this->shippingOptions)) {
            $this->shippingFeedback = 'Tidak ada layanan yang tersedia. Cek kembali kota atau kurir.';
        } else {
            $this->shippingFeedback = sprintf(
                '%d opsi ongkir ditemukan untuk estimasi berat %.1f kg.',
                count($this->shippingOptions),
                $weight / 1000
            );
        }
    }

    public function chooseShipping(int $index): void
    {
        $option = $this->shippingOptions[$index] ?? null;

        if (! $option) {
            return;
        }

        $label = $option['provider'] . ' ' . $option['service'] . ' (' . $option['etd'] . ')';

        $cart = $this->cartManager->current();
        $this->cartManager->updateShipping($cart, $label, $option['cost']);

        $this->afterCartMutation('Ongkos kirim diperbarui.');
    }

    public function render()
    {
        return view('livewire.cart.page', [
            'cart' => $this->cartState,
            'shippingOptions' => $this->shippingOptions,
            'shippingFeedback' => $this->shippingFeedback,
            'flashMessage' => $this->flashMessage,
        ]);
    }

    private function afterCartMutation(string $message): void
    {
        $this->flashMessage = $message;
        $this->dispatch('cart-updated');
    }

    /**
     * @return array{
     *   id: int,
     *   items: array<int, array<string, mixed>>,
     *   saved_items: array<int, array<string, mixed>>,
     *   totals: array<string, int>,
     *   coupon: array{code: ?string, label: ?string},
     *   shipping: array{service: ?string}
     * }
     */
    private function presentCart(Cart $cart): array
    {
        $items = $cart->items->map(fn (CartItem $item) => $this->presentItem($item));

        return [
            'id' => $cart->id,
            'items' => $items->where('saved_for_later', false)->values()->all(),
            'saved_items' => $items->where('saved_for_later', true)->values()->all(),
            'totals' => [
                'subtotal' => $cart->subtotal,
                'discount' => $cart->discount_total,
                'shipping' => $cart->shipping_total,
                'tax' => $cart->tax_total,
                'grand' => $cart->grand_total,
            ],
            'coupon' => [
                'code' => optional($cart->coupon)->code,
                'label' => optional($cart->coupon)->code
                    ? Str::upper($cart->coupon->code) . ' • ' . $this->couponLabel($cart)
                    : null,
            ],
            'shipping' => [
                'service' => $cart->shipping_service,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentItem(CartItem $item): array
    {
        $product = $item->product;
        $variant = $item->variant;
        $image = collect($product?->images ?? [])->first();

        $variantLabel = trim(collect([
            $variant?->color_name,
            $variant?->size ? 'EU ' . $variant->size : null,
        ])->filter()->implode(' • '));

        return [
            'id' => $item->id,
            'product_id' => $item->product_id,
            'name' => $product?->name ?? 'Produk tidak tersedia',
            'slug' => $product?->slug,
            'brand' => $product?->brand?->name,
            'variant_label' => $variantLabel !== '' ? $variantLabel : null,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
            'line_total' => $item->line_total,
            'image' => $image,
            'saved_for_later' => $item->saved_for_later,
            'max_stock' => $variant?->stock_quantity ?? null,
        ];
    }

    private function couponLabel(Cart $cart): string
    {
        if (! $cart->coupon) {
            return '';
        }

        return match ($cart->coupon->type) {
            'percent' => $cart->coupon->value . '% off',
            'fixed' => 'Potongan Rp ' . number_format($cart->coupon->value, 0, ',', '.'),
            'free_shipping' => 'Gratis ongkir',
            default => 'Kupon aktif',
        };
    }
}
