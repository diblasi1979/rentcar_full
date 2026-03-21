<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_coverages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('policy_number');
            $table->string('endorsement_number')->nullable();
            $table->string('insured_last_name');
            $table->string('insured_first_name');
            $table->string('insured_document_type', 50);
            $table->string('insured_document_number', 50);
            $table->string('insurance_company');
            $table->string('contact_phone', 50)->nullable();
            $table->string('electronic_payment_code')->nullable();
            $table->date('valid_from');
            $table->date('valid_to');
            $table->string('policy_pdf')->nullable();
            $table->string('credential_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_coverages');
    }
};
