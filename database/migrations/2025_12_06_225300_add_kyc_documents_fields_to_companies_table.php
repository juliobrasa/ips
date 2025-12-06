<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Tipo de entidad (persona física o jurídica)
            $table->enum('entity_type', ['individual', 'company'])->default('company')->after('company_type');

            // Documentos de identidad
            $table->string('identity_document_type')->nullable()->after('entity_type'); // dni, nie, passport, nif, cif
            $table->string('identity_document_number')->nullable()->after('identity_document_type');
            $table->string('identity_document_file')->nullable()->after('identity_document_number'); // Ruta del archivo
            $table->timestamp('identity_document_uploaded_at')->nullable()->after('identity_document_file');

            // Documento KYC firmado
            $table->string('kyc_signed_document')->nullable()->after('identity_document_uploaded_at'); // Ruta del PDF firmado
            $table->timestamp('kyc_signed_uploaded_at')->nullable()->after('kyc_signed_document');

            // Representante legal (para empresas)
            $table->string('legal_representative_name')->nullable()->after('kyc_signed_uploaded_at');
            $table->string('legal_representative_id')->nullable()->after('legal_representative_name');
            $table->string('legal_representative_position')->nullable()->after('legal_representative_id');

            // Fecha de revisión por admin
            $table->timestamp('kyc_reviewed_at')->nullable()->after('kyc_notes');
            $table->foreignId('kyc_reviewed_by')->nullable()->after('kyc_reviewed_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['kyc_reviewed_by']);
            $table->dropColumn([
                'entity_type',
                'identity_document_type',
                'identity_document_number',
                'identity_document_file',
                'identity_document_uploaded_at',
                'kyc_signed_document',
                'kyc_signed_uploaded_at',
                'legal_representative_name',
                'legal_representative_id',
                'legal_representative_position',
                'kyc_reviewed_at',
                'kyc_reviewed_by',
            ]);
        });
    }
};
