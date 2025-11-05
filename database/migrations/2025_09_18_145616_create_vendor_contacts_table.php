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
            $table->unsignedBigInteger('business_id')->nullable();
            $table->string('business_name')->nullable();

            $table->enum('profile_type', ['business', 'individual'])->default('individual');
            $table->string('department', 100)->nullable()->after('label');
            $table->string('designation', 100)->nullable()->after('department');
            $table->string('contact_person_type', 100)->nullable()->after('profile_type');
            $table->string('contact_person_name', 200)->nullable()->after('contact_person_type');
            $table->string('extension', 20)->nullable()->after('value');

            $table->enum('contact_type', ['email', 'phone', 'whatsapp', 'telegram', 'fax', 'other'])
                ->default('whatsapp');
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

            $table->foreign('business_id')
                ->references('id')->on('vendor_business_profiles')
                ->nullOnDelete();
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
