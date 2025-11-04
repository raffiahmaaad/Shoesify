<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::updateOrCreate(
            ['code' => 'WELCOME10'],
            [
                'type' => 'percent',
                'value' => 10,
                'max_discount' => 50000,
                'min_subtotal' => 250000,
                'usage_limit' => 500,
                'starts_at' => Carbon::now()->subDay(),
                'ends_at' => Carbon::now()->addMonths(3),
                'is_active' => true,
            ],
        );

        Coupon::updateOrCreate(
            ['code' => 'FREESHIP'],
            [
                'type' => 'free_shipping',
                'value' => 0,
                'max_discount' => 30000,
                'min_subtotal' => 150000,
                'usage_limit' => null,
                'starts_at' => Carbon::now()->subDay(),
                'ends_at' => Carbon::now()->addMonths(6),
                'is_active' => true,
            ],
        );
    }
}
