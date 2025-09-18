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
        Schema::create('vendor_payment_accounts', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AI PK
            $table->unsignedBigInteger('tenant_id'); // Multi-tenant scope
            $table->unsignedBigInteger('vendor_id'); // FK → students.id

            $table->enum('method', ['bank', 'upi'])->nullable();; // Payment method type
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
            $table->boolean('is_primary')->default(false); // One primary per entity
            $table->boolean('is_default_payout')->default(false); // Default for refunds/payouts

            // --- Bank fields ---
            $table->string('account_holder', 150)->nullable();
            $table->string('bank_name', 120)->nullable();
            $table->string('branch_name', 120)->nullable();
            $table->string('ifsc_code', 15)->nullable();
            $table->string('swift_code', 15)->nullable();
            $table->string('account_number_mask', 8)->nullable(); // last 4–8 only
            $table->binary('account_number_hash')->nullable(); // secure hash (SHA-256)

            // --- UPI fields ---
            $table->string('upi_vpa', 120)->nullable();
            $table->enum('upi_verified', ['no', 'yes'])->default('no');

            // --- Verification & Audit ---
            $table->enum('verified', ['no', 'yes'])->default('no');
            $table->dateTime('verified_at', 6)->nullable();
            $table->enum('verification_method', ['otp', 'penny_drop', 'statement', 'manual', 'provider'])->nullable();
            $table->enum('source', ['web', 'mobile', 'import', 'api', 'other'])->nullable();
            $table->json('meta')->nullable();

            $table->unsignedInteger('row_version')->default(0); 

            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at', 6)->nullable(); 

            // 🔑 Foreign key
            $table->foreign('vendor_id')
                ->references('id')->on('vendors')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_payment_accounts');
    }
};
