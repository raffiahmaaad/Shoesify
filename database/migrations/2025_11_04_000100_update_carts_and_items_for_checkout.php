<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table): void {
            $table->foreignId('applied_coupon_id')
                ->nullable()
                ->after('status')
                ->constrained('coupons')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->string('shipping_service')
                ->nullable()
                ->after('shipping_total');

            $table->unsignedInteger('weight_total')->default(0)->after('tax_total');
        });

        Schema::table('cart_items', function (Blueprint $table): void {
            $table->boolean('saved_for_later')
                ->default(false)
                ->after('line_total');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table): void {
            $table->dropColumn('saved_for_later');
        });

        Schema::table('carts', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('applied_coupon_id');
            $table->dropColumn('shipping_service');
            $table->dropColumn('weight_total');
        });
    }
};
