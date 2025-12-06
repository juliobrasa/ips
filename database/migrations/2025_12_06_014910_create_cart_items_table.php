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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subnet_id')->constrained()->onDelete('cascade');
            $table->integer('lease_months')->default(1);
            $table->timestamp('reserved_until')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'subnet_id']);
            $table->index('reserved_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
