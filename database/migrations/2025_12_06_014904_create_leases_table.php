<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subnet_id')->constrained()->onDelete('cascade');
            $table->foreignId('lessee_company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('holder_company_id')->constrained('companies')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('auto_renew')->default(true);
            $table->decimal('monthly_price', 10, 2);
            $table->decimal('platform_fee_percentage', 5, 2)->default(10.00);
            $table->enum('status', [
                'pending_payment',
                'pending_assignment',
                'active',
                'expired',
                'terminated',
                'cancelled'
            ])->default('pending_payment');
            $table->string('assigned_asn', 20)->nullable();
            $table->timestamp('loa_generated_at')->nullable();
            $table->timestamp('roa_created_at')->nullable();
            $table->text('termination_reason')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};
