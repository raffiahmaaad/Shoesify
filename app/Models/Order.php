<?php

declare(strict_types=1);

namespace App\Models;

use App\Notifications\OrderPlacedNotification;
use App\Notifications\OrderStatusUpdatedNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cart_id',
        'order_number',
        'status',
        'subtotal',
        'discount_total',
        'shipping_total',
        'shipping_service',
        'tracking_number',
        'tax_total',
        'grand_total',
        'payment_method',
        'payment_status',
        'payment_payload',
        'shipping_address',
        'billing_address',
        'notes',
        'placed_at',
        'paid_at',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'discount_total' => 'integer',
        'shipping_total' => 'integer',
        'tax_total' => 'integer',
        'grand_total' => 'integer',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'payment_payload' => 'array',
        'placed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::created(function (Order $order): void {
            $order->loadMissing('user');

            if ($order->user) {
                $order->user->notify(new OrderPlacedNotification($order));
            }
        });

        static::updated(function (Order $order): void {
            $order->loadMissing('user');

            $fields = collect([
                'status',
                'payment_status',
                'tracking_number',
                'shipping_service',
            ])->filter(fn ($field) => $order->wasChanged($field));

            if ($fields->isNotEmpty() && $order->user) {
                $changes = $fields->mapWithKeys(fn ($field) => [$field => $order->{$field}])->toArray();
                $order->user->notify(new OrderStatusUpdatedNotification($order, $changes));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
