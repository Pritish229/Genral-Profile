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
        Schema::create('vendor_contacts', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, AI, PK
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('vendor_id');

            $table->enum('contact_type', ['email', 'phone', 'whatsapp', 'telegram', 'fax', 'other'])->default('whatsapp');
            $table->string('value', 180)->nullable();
            $table->string('normalized_value', 180)->nullable();
            $table->char('country_code', 2)->nullable();
            $table->string('label', 80)->nullable();

            $table->boolean('is_primary')->default(false);
            $table->boolean('is_emergency')->default(false);

            $table->enum('verified', ['no', 'yes'])->default('no');
            $table->dateTime('verified_at', 6)->nullable();
            $table->enum('verification_method', ['otp', 'link', 'manual', 'provider'])->nullable();
            $table->enum('source', ['web', 'mobile', 'import', 'api', 'other'])->nullable();

            $table->unsignedInteger('row_version')->default(0);

            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at', 6)->nullable();

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
        Schema::dropIfExists('vendor_contacts');
    }
};
