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
        Schema::create('customer_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('business_id')->nullable();
            $table->string('business_name')->nullable();
            $table->enum('profile_type', ['business', 'individual',])->default('individual');

            $table->string('document_type', 80)->nullable();;
            $table->string('document_number', 100)->nullable();
            $table->binary('document_number_hash')->nullable();
            $table->string('document_number_mask', 12)->nullable();

            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('issuing_authority', 150)->nullable();

            $table->string('file_name', 200)->nullable();
            $table->string('file_url', 500)->nullable();
            $table->string('file_type', 20)->nullable();

            $table->enum('verified', ['no', 'yes', 'pending'])->default('pending');
            $table->dateTime('verified_at', 6)->nullable();
            $table->string('verified_by', 120)->nullable();
            $table->string('remarks', 255)->nullable();
            $table->enum('source', ['web', 'mobile', 'import', 'api', 'other'])->nullable();

            $table->json('meta')->nullable();

            $table->unsignedInteger('row_version')->default(0);
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('business_id')->references('id')->on('customer_business_profiles')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_documents');
    }
};
