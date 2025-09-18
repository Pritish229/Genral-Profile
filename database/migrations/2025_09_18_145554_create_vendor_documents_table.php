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
        Schema::create('vendor_documents', function (Blueprint $table) {
            $table->id();
        $table->unsignedBigInteger('tenant_id'); 
        $table->unsignedBigInteger('vendor_id'); 

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

        $table->enum('verified', ['no','yes','pending'])->default('pending');
        $table->dateTime('verified_at', 6)->nullable();
        $table->string('verified_by', 120)->nullable();  
        $table->string('remarks', 255)->nullable();      
        $table->enum('source', ['web','mobile','import','api','other'])->nullable();

        $table->json('meta')->nullable();

        $table->unsignedInteger('row_version')->default(0);
        $table->timestamps(6);
        $table->softDeletes('deleted_at', 6);

        $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_documents');
    }
};
