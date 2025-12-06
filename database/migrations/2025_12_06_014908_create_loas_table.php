<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained()->onDelete('cascade');
            $table->string('loa_number')->unique();
            $table->string('ip_range');
            $table->string('authorized_asn', 20);
            $table->date('valid_from');
            $table->date('valid_until');
            $table->string('holder_company_name');
            $table->string('lessee_company_name');
            $table->string('file_path')->nullable();
            $table->string('signature_hash')->nullable();
            $table->enum('status', ['active', 'expired', 'revoked'])->default('active');
            $table->timestamps();

            $table->index('status');
            $table->index('valid_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loas');
    }
};
