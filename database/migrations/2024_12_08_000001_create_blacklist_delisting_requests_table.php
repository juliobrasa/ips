<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blacklist_delisting_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subnet_id')->constrained()->onDelete('cascade');
            $table->string('blocklist');
            $table->enum('status', ['pending', 'in_progress', 'delisted', 'failed', 'manual_required'])->default('pending');
            $table->string('contact_email')->nullable();
            $table->text('reason')->nullable();
            $table->string('request_url')->nullable();
            $table->text('response_message')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('delisted_at')->nullable();
            $table->timestamps();

            $table->index(['subnet_id', 'blocklist']);
            $table->index('status');
            $table->index('requested_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blacklist_delisting_requests');
    }
};
