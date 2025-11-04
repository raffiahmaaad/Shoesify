<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'product_id', 'product_variant_id'], 'wishlist_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
