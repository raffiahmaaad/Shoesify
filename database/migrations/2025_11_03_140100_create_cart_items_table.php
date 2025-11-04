<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('unit_price');
            $table->unsignedInteger('line_total');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['cart_id', 'product_id', 'product_variant_id'], 'cart_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
