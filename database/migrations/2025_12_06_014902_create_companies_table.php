<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('legal_name')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('country', 2);
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->enum('company_type', ['holder', 'lessee', 'both'])->default('lessee');
            $table->enum('kyc_status', ['pending', 'in_review', 'approved', 'rejected'])->default('pending');
            $table->json('kyc_documents')->nullable();
            $table->timestamp('kyc_approved_at')->nullable();
            $table->text('kyc_notes')->nullable();
            $table->string('payout_method')->nullable();
            $table->json('payout_details')->nullable();
            $table->timestamps();

            $table->index('kyc_status');
            $table->index('company_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
