<?php

declare(strict_types=1);

namespace App\Livewire\Checkout;

use App\Models\Address;
use App\Models\Cart;
use App\Services\CartManager;
use App\Services\Checkout\CheckoutProcessor;
use App\Services\Payments\MidtransGateway;
use App\Services\Shipping\ShippingEstimator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;
use RuntimeException;

class Wizard extends Component
{
    public string $step = 'address';

    public array $cart = [];
    public ?int $cartId = null;

    public array $addressBook = [];
    public ?int $selectedAddressId = null;
    public array $addressForm = [
        'label' => '',
        'recipient_name' => '',
        'phone' => '',
        'address_line1' => '',
        'address_line2' => '',
        'city' => '',
        'province' => '',
        'postal_code' => '',
        'country' => 'Indonesia',
        'save_to_book' => true,
    ];

    public array $shippingAddress = [];
    public array $shippingForm = [
        'destination' => '',
        'postal_code' => '',
        'courier' => null,
    ];
    public array $shippingOptions = [];
    public ?int $selectedShippingIndex = null;
    public array $selectedShipping = [];
    public ?string $shippingFeedback = null;

    public string $paymentMethod = '';
    public array $paymentMeta = [];
    public bool $termsAccepted = false;

    public ?string $orderNumber = null;
    public ?int $orderId = null;

    private CartManager $cartManager;
    private ShippingEstimator $shippingEstimator;
    private MidtransGateway $midtransGateway;

    public function boot(
        CartManager $cartManager,
        ShippingEstimator $shippingEstimator,
        MidtransGateway $midtransGateway,
    ): void {
        $this->cartManager = $cartManager;
        $this->shippingEstimator = $shippingEstimator;
        $this->midtransGateway = $midtransGateway;
    }

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);

        $this->hydrateCart();
        $this->loadAddressBook();

        if (! empty($this->addressBook)) {
            $default = collect($this->addressBook)->firstWhere('is_default', true) ?? $this->addressBook[0];
            $this->selectedAddressId = $default['id'];
        }
    }

    #[On('cart-updated')]
    public function hydrateCart(): void
    {
        $cart = $this->cartManager->current();
        $cart->loadMissing([
            'items' => fn ($query) => $query->with(['product', 'variant']),
            'coupon',
        ]);

        $activeCount = $cart->items->where('saved_for_later', false)->count();
        if ($activeCount === 0 && $this->step !== 'complete') {
            session()->flash('cart.notice', 'Keranjang kamu kosong. Silakan tambah produk terlebih dahulu.');
            $this->redirectRoute('cart.index');
            return;
        }

        $this->cartId = $cart->id;
        $this->cart = $this->presentCart($cart);
    }

    public function loadAddressBook(): void
    {
        $user = Auth::user();
        $this->addressBook = $user
            ? $user->addresses()
                ->orderByDesc('is_default')
                ->orderByDesc('created_at')
                ->get()
                ->map(fn (Address $address) => $address->only([
                    'id',
                    'label',
                    'recipient_name',
                    'phone',
                    'address_line1',
                    'address_line2',
                    'city',
                    'province',
                    'postal_code',
                    'country',
                    'is_default',
                ]))
                ->all()
            : [];
    }

    public function useNewAddress(): void
    {
        $this->selectedAddressId = null;
        $this->addressForm['label'] = 'Alamat Baru';
    }

    public function proceedAddress(): void
    {
        if ($this->selectedAddressId) {
            $address = collect($this->addressBook)->firstWhere('id', $this->selectedAddressId);
            if (! $address) {
                throw ValidationException::withMessages([
                    'selectedAddressId' => 'Alamat tidak ditemukan.',
                ]);
            }
            $this->shippingAddress = $address;
        } else {
            $validated = $this->validate([
                'addressForm.label' => ['required', 'string', 'max:60'],
                'addressForm.recipient_name' => ['required', 'string', 'max:120'],
                'addressForm.phone' => ['required', 'string', 'max:30'],
                'addressForm.address_line1' => ['required', 'string', 'max:255'],
                'addressForm.address_line2' => ['nullable', 'string', 'max:255'],
                'addressForm.city' => ['required', 'string', 'max:120'],
                'addressForm.province' => ['required', 'string', 'max:120'],
                'addressForm.postal_code' => ['required', 'string', 'max:20'],
                'addressForm.country' => ['required', 'string', 'max:80'],
                'addressForm.save_to_book' => ['boolean'],
            ])['addressForm'];

            $validated['is_default'] = false;

            if ($validated['save_to_book']) {
                $address = Auth::user()->addresses()->create($validated);
                $validated['id'] = $address->id;
                $this->loadAddressBook();
            } else {
                $validated['id'] = null;
            }

            $this->shippingAddress = $validated;
        }

        $this->shippingForm['destination'] = trim(($this->shippingAddress['city'] ?? '') . ', ' . ($this->shippingAddress['province'] ?? ''));
        $this->shippingForm['postal_code'] = $this->shippingAddress['postal_code'] ?? '';
        $this->shippingForm['courier'] = null;
        $this->shippingOptions = [];
        $this->selectedShippingIndex = null;
        $this->selectedShipping = [];
        $this->shippingFeedback = null;
        $this->step = 'shipping';
    }

    public function backToAddress(): void
    {
        $this->step = 'address';
    }

    public function estimateShipping(): void
    {
        $destination = trim($this->shippingForm['destination']);
        $postalCode = trim($this->shippingForm['postal_code']);

        if ($destination === '' || $postalCode === '') {
            $this->shippingFeedback = 'Lengkapi kota/kabupaten dan kode pos terlebih dahulu.';
            $this->shippingOptions = [];
            return;
        }

        $cartWeight = max(1, $this->cart['totals']['weight'] ?? 0);

        $this->shippingOptions = $this->shippingEstimator->estimate([
            'destination' => $destination,
            'postal_code' => $postalCode,
            'courier' => $this->shippingForm['courier'],
            'weight' => $cartWeight,
        ]);

        if (empty($this->shippingOptions)) {
            $this->shippingFeedback = 'Tidak ada layanan ditemukan untuk kombinasi tujuan & kurir tersebut.';
        } else {
            $this->shippingFeedback = sprintf(
                'Menampilkan %d opsi pengiriman. Berat total %.1f kg.',
                count($this->shippingOptions),
                $cartWeight / 1000
            );
        }
    }

    public function proceedShipping(): void
    {
        if ($this->selectedShippingIndex === null || ! isset($this->shippingOptions[$this->selectedShippingIndex])) {
            throw ValidationException::withMessages([
                'selectedShippingIndex' => 'Pilih salah satu layanan pengiriman terlebih dahulu.',
            ]);
        }

        $option = $this->shippingOptions[$this->selectedShippingIndex];
        $label = $option['provider'] . ' ' . $option['service'] . ' (' . $option['etd'] . ')';

        $cart = Cart::query()->findOrFail($this->cartId);
        $this->cartManager->updateShipping($cart, $label, $option['cost']);

        $this->selectedShipping = $option;
        $this->hydrateCart();

        $this->step = 'payment';
    }

    public function backToShipping(): void
    {
        $this->step = 'shipping';
    }

    public function selectPayment(string $method): void
    {
        $this->paymentMethod = $method;

        if ($method === 'midtrans') {
            $this->paymentMeta = $this->midtransGateway->createTransaction([
                'order_id' => 'CHK-' . $this->cartId,
                'amount' => $this->cart['totals']['grand'],
                'customer' => [
                    'name' => Auth::user()->name ?? 'Guest',
                    'email' => Auth::user()->email ?? 'guest@example.com',
                    'phone' => $this->shippingAddress['phone'] ?? null,
                ],
            ]);
        } else {
            $this->paymentMeta = [];
        }
    }

    public function proceedPayment(): void
    {
        if ($this->paymentMethod === '') {
            throw ValidationException::withMessages([
                'paymentMethod' => 'Pilih metode pembayaran terlebih dahulu.',
            ]);
        }

        $this->step = 'review';
    }

    public function backToPayment(): void
    {
        $this->step = 'payment';
    }

    public function placeOrder(CheckoutProcessor $processor): void
    {
        $this->validate([
            'termsAccepted' => ['accepted'],
        ]);

        if (! $this->cartId) {
            throw new RuntimeException('Keranjang tidak ditemukan.');
        }

        $shippingAddress = $this->shippingAddress;
        if ($this->selectedShipping) {
            $shippingAddress['shipping_service'] = $this->selectedShipping['provider'] . ' ' . $this->selectedShipping['service'];
            $shippingAddress['shipping_note'] = $this->selectedShipping['etd'];
        }

        $order = $processor->placeOrder(
            Auth::user(),
            $this->cartId,
            [
                'shipping_address' => $shippingAddress,
                'payment_method' => $this->paymentMethod,
                'payment_payload' => $this->paymentMeta,
            ],
        );

        $this->orderId = $order->id;
        $this->orderNumber = $order->order_number;
        $this->step = 'complete';

        $this->selectedShipping = [];
        $this->shippingOptions = [];
        $this->paymentMeta = [];
        $this->termsAccepted = false;

        $this->dispatch('cart-updated');

        // Prime cart manager for next session
        $newCart = $this->cartManager->current();
        $this->cartId = $newCart->id;
        $this->cart = $this->presentCart($newCart);
    }

    public function render()
    {
        return view('livewire.checkout.wizard');
    }

    private function presentCart(Cart $cart): array
    {
        $items = $cart->items->map(function ($item) {
            $image = collect($item->product?->images ?? [])->first();
            $variant = trim(collect([
                $item->variant?->color_name,
                $item->variant?->size ? 'EU ' . $item->variant?->size : null,
            ])->filter()->implode(' • '));

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product?->name ?? 'Produk tidak tersedia',
                'slug' => $item->product?->slug,
                'image' => $image,
                'variant_label' => $variant ?: null,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'line_total' => $item->line_total,
                'saved_for_later' => (bool) $item->saved_for_later,
            ];
        });

        return [
            'items' => $items->where('saved_for_later', false)->values()->all(),
            'totals' => [
                'subtotal' => $cart->subtotal,
                'discount' => $cart->discount_total,
                'shipping' => $cart->shipping_total,
                'tax' => $cart->tax_total,
                'grand' => $cart->grand_total,
                'weight' => $cart->weight_total,
            ],
        ];
    }
}
