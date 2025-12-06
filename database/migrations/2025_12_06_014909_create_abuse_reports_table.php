<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abuse_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subnet_id')->constrained()->onDelete('cascade');
            $table->foreignId('lease_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['spam', 'phishing', 'malware', 'ddos', 'scraping', 'fraud', 'other']);
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->string('source')->nullable();
            $table->text('description');
            $table->json('evidence')->nullable();
            $table->enum('status', ['open', 'investigating', 'resolved', 'dismissed'])->default('open');
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('status');
            $table->index('severity');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abuse_reports');
    }
};
