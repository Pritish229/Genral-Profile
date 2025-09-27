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
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('business_id')->nullable();
            $table->string('business_name')->nullable();
            $table->enum('profile_type', ['business', 'individual'])->default('individual');

            $table->enum('method', ['bank', 'upi'])->nullable();
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_default_payout')->default(false);

            $table->string('account_holder', 150)->nullable();
            $table->string('bank_name', 120)->nullable();
            $table->string('branch_name', 120)->nullable();
            $table->string('ifsc_code', 15)->nullable();
            $table->string('swift_code', 15)->nullable();
            $table->text('account_number')->nullable(); // store encrypted safely
            $table->string('account_number_mask', 50)->nullable();
            $table->string('account_number_hash', 64)->nullable();

            $table->string('upi_vpa', 120)->nullable();
            $table->enum('upi_verified', ['no', 'yes'])->default('no');

            $table->enum('verified', ['no', 'yes'])->default('no');
            $table->dateTime('verified_at', 6)->nullable();
            $table->enum('verification_method', ['otp', 'penny_drop', 'statement', 'manual', 'provider'])->nullable();
            $table->enum('source', ['web', 'mobile', 'import', 'api', 'other'])->nullable();
            $table->json('meta')->nullable();

            $table->unsignedInteger('row_version')->default(0);

            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at', 6)->nullable();

            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('business_id')->references('id')->on('vendor_business_profiles')->nullOnDelete();
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
