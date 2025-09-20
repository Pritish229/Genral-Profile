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
        Schema::create('customer_business_profiles', function (Blueprint $table) {
             $table->bigIncrements('id'); // PK

            $table->unsignedBigInteger('tenant_id'); // FK -> tenants.id
            $table->unsignedBigInteger('customer_id'); // FK -> customers.id

            $table->string('legal_name', 180); // Registered name
            $table->string('trade_name', 180)->nullable(); // Brand/DBA
            $table->string('industry', 120)->nullable(); // standardize in app
            $table->date('incorporation_date')->nullable();

            $table->enum('business_size', ['micro', 'sme', 'enterprise'])->nullable();
            $table->string('website', 200)->nullable();

            $table->string('primary_contact_name', 150)->nullable();
            $table->string('primary_contact_email', 150)->nullable();
            $table->string('primary_contact_phone', 30)->nullable(); // E.164 format

            $table->string('billing_email', 150)->nullable();
            $table->string('billing_phone', 30)->nullable();

            $table->string('gst_number', 15)->nullable()->index();
            $table->string('pan_number', 15)->nullable()->index();
            $table->string('cin_number', 25)->nullable()->index();

            $table->decimal('credit_limit', 14, 2)->default(0); // B2B terms
            $table->unsignedSmallInteger('payment_terms_days')->nullable(); // e.g., 7/15/30

            $table->string('account_manager', 120)->nullable(); // internal owner

            $table->unsignedInteger('row_version')->default(0); // optimistic lock

            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at', 6)->nullable(); // soft delete

            // Constraints
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->unique(['tenant_id', 'customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_business_profiles');
    }
};
