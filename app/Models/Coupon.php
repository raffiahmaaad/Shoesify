<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'max_discount',
        'min_subtotal',
        'usage_limit',
        'usage_count',
        'is_active',
        'metadata',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'value' => 'integer',
        'max_discount' => 'integer',
        'min_subtotal' => 'integer',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'is_active' => 'boolean',
        'metadata' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'applied_coupon_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        $now = Carbon::now();

        return $query
            ->where('is_active', true)
            ->where(function (Builder $builder) use ($now): void {
                $builder
                    ->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function (Builder $builder) use ($now): void {
                $builder
                    ->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            });
    }

    public function isWithinUsageLimit(): bool
    {
        if ($this->usage_limit === null) {
            return true;
        }

        return $this->usage_count < $this->usage_limit;
    }
}
