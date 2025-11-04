<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->json('payment_payload')->nullable()->after('payment_status');
            $table->string('shipping_service')->nullable()->after('shipping_total');
            $table->string('tracking_number')->nullable()->after('shipping_service');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn('payment_payload');
            $table->dropColumn('shipping_service');
            $table->dropColumn('tracking_number');
        });
    }
};
