<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Notification preferences
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('notification_type');
            $table->boolean('email_enabled')->default(true);
            $table->boolean('database_enabled')->default(true);
            $table->boolean('webhook_enabled')->default(false);
            $table->boolean('sms_enabled')->default(false);
            $table->boolean('telegram_enabled')->default(false);
            $table->boolean('slack_enabled')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'notification_type']);
        });

        // Webhook endpoints
        Schema::create('webhook_endpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('url');
            $table->string('secret');
            $table->json('events')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('failure_count')->default(0);
            $table->integer('last_response_code')->nullable();
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();
        });

        // Payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('EUR');
            $table->string('payment_method');
            $table->string('transaction_id')->nullable();
            $table->string('status');
            $table->decimal('refunded_amount', 12, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('transaction_id');
        });

        // Payment methods (stored cards)
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('stripe_payment_method_id');
            $table->string('type');
            $table->string('card_brand')->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->unsignedTinyInteger('card_exp_month')->nullable();
            $table->unsignedSmallInteger('card_exp_year')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Support tickets
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->string('subject');
            $table->string('category');
            $table->string('priority')->default('medium');
            $table->string('status')->default('open');
            $table->foreignId('related_lease_id')->nullable()->constrained('leases')->onDelete('set null');
            $table->foreignId('related_subnet_id')->nullable()->constrained('subnets')->onDelete('set null');
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['assigned_to', 'status']);
        });

        // Ticket messages
        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message');
            $table->boolean('is_internal')->default(false);
            $table->json('attachments')->nullable();
            $table->timestamps();
        });

        // Referrals
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referred_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->decimal('commission_rate', 5, 2)->default(10);
            $table->decimal('total_earnings', 12, 2)->default(0);
            $table->integer('total_orders')->default(0);
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();

            $table->unique('referred_id');
            $table->index(['referrer_id', 'status']);
        });

        // Referral rewards
        Schema::create('referral_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->constrained()->onDelete('cascade');
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('source_type');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['referrer_id', 'status']);
        });

        // IP Quality Scores history
        Schema::create('ip_quality_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subnet_id')->constrained()->onDelete('cascade');
            $table->integer('score');
            $table->json('factors')->nullable();
            $table->json('blacklists_found')->nullable();
            $table->integer('abuse_reports_count')->default(0);
            $table->timestamp('checked_at');
            $table->timestamps();

            $table->index(['subnet_id', 'checked_at']);
        });

        // Currency rates
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('from_currency', 3);
            $table->string('to_currency', 3);
            $table->decimal('rate', 12, 6);
            $table->timestamp('valid_from');
            $table->timestamp('valid_until')->nullable();
            $table->timestamps();

            $table->index(['from_currency', 'to_currency', 'valid_from']);
        });

        // Automations/scheduled tasks
        Schema::create('automation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('trigger_type');
            $table->json('trigger_conditions');
            $table->string('action_type');
            $table->json('action_params');
            $table->boolean('is_active')->default(true);
            $table->integer('execution_count')->default(0);
            $table->timestamp('last_executed_at')->nullable();
            $table->timestamps();
        });

        // Add fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->after('remember_token');
            $table->string('referral_code', 10)->nullable()->unique()->after('stripe_customer_id');
            $table->string('telegram_chat_id')->nullable()->after('referral_code');
            $table->string('slack_webhook_url')->nullable()->after('telegram_chat_id');
            $table->string('preferred_currency', 3)->default('EUR')->after('slack_webhook_url');
        });

        // Add geofeed field to subnets
        Schema::table('subnets', function (Blueprint $table) {
            $table->text('geofeed_data')->nullable()->after('notes');
            $table->integer('quality_score')->nullable()->after('reputation_score');
        });
    }

    public function down(): void
    {
        Schema::table('subnets', function (Blueprint $table) {
            $table->dropColumn(['geofeed_data', 'quality_score']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_customer_id',
                'referral_code',
                'telegram_chat_id',
                'slack_webhook_url',
                'preferred_currency',
            ]);
        });

        Schema::dropIfExists('automation_rules');
        Schema::dropIfExists('currency_rates');
        Schema::dropIfExists('ip_quality_scores');
        Schema::dropIfExists('referral_rewards');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('ticket_messages');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('webhook_endpoints');
        Schema::dropIfExists('notification_preferences');
    }
};
