<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ripe_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name')->comment('Friendly name for credentials');
            $table->text('api_key')->comment('Encrypted RIPE API key');
            $table->string('maintainer')->nullable()->comment('Associated maintainer object');
            $table->json('allowed_object_types')->nullable()->comment('Object types this credential can manage');
            $table->boolean('is_active')->default(true);
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('expires_at')->nullable()->comment('API key expiration');
            $table->timestamps();

            $table->index(['company_id', 'is_active']);
        });

        // Add RIPE-related fields to subnets table
        Schema::table('subnets', function (Blueprint $table) {
            $table->string('ripe_inetnum_key')->nullable()->after('notes')->comment('RIPE inetnum primary key');
            $table->string('ripe_netname')->nullable()->after('ripe_inetnum_key');
            $table->string('ripe_maintainer')->nullable()->after('ripe_netname');
            $table->string('ripe_org')->nullable()->after('ripe_maintainer');
            $table->string('ripe_status')->nullable()->after('ripe_org');
            $table->timestamp('ripe_last_synced_at')->nullable()->after('ripe_status');
        });

        // Add RIPE-related fields to companies table
        Schema::table('companies', function (Blueprint $table) {
            $table->string('ripe_org_id')->nullable()->after('notes')->comment('RIPE Organisation ID');
            $table->string('ripe_admin_c')->nullable()->after('ripe_org_id')->comment('RIPE admin contact nic-hdl');
            $table->string('ripe_tech_c')->nullable()->after('ripe_admin_c')->comment('RIPE tech contact nic-hdl');
            $table->string('ripe_default_maintainer')->nullable()->after('ripe_tech_c');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subnets', function (Blueprint $table) {
            $table->dropColumn([
                'ripe_inetnum_key',
                'ripe_netname',
                'ripe_maintainer',
                'ripe_org',
                'ripe_status',
                'ripe_last_synced_at',
            ]);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'ripe_org_id',
                'ripe_admin_c',
                'ripe_tech_c',
                'ripe_default_maintainer',
            ]);
        });

        Schema::dropIfExists('ripe_credentials');
    }
};
