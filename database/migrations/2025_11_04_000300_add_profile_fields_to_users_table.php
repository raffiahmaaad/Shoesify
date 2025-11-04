<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('phone', 30)->nullable()->after('email');
            $table->date('birth_date')->nullable()->after('phone');
            $table->string('gender', 20)->nullable()->after('birth_date');
            $table->string('avatar_path')->nullable()->after('gender');
            $table->text('bio')->nullable()->after('avatar_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'phone',
                'birth_date',
                'gender',
                'avatar_path',
                'bio',
            ]);
        });
    }
};
