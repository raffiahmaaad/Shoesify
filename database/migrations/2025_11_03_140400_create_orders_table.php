<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('cart_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string('order_number')->unique();
            $table->string('status')->default('pending');
            $table->unsignedInteger('subtotal')->default(0);
            $table->unsignedInteger('discount_total')->default(0);
            $table->unsignedInteger('shipping_total')->default(0);
            $table->unsignedInteger('tax_total')->default(0);
            $table->unsignedInteger('grand_total')->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('unpaid');
            $table->json('shipping_address');
            $table->json('billing_address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
