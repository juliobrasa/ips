<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subnets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->tinyInteger('cidr')->unsigned();
            $table->enum('rir', ['RIPE', 'ARIN', 'LACNIC', 'APNIC', 'AFRINIC']);
            $table->string('geolocation_country', 2)->nullable();
            $table->string('geolocation_city')->nullable();
            $table->decimal('price_per_ip_monthly', 10, 4);
            $table->integer('min_lease_months')->default(1);
            $table->enum('status', [
                'pending_verification',
                'verification_failed',
                'available',
                'reserved',
                'leased',
                'suspended',
                'terminated'
            ])->default('pending_verification');
            $table->string('verification_token')->nullable();
            $table->timestamp('ownership_verified_at')->nullable();
            $table->boolean('rpki_delegated')->default(false);
            $table->boolean('auto_renewal')->default(true);
            $table->tinyInteger('reputation_score')->unsigned()->default(100);
            $table->timestamp('last_reputation_check')->nullable();
            $table->json('blocklist_results')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['ip_address', 'cidr']);
            $table->index('status');
            $table->index('rir');
            $table->index('geolocation_country');
            $table->index('price_per_ip_monthly');
            $table->index('reputation_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subnets');
    }
};
