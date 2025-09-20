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
        Schema::create('customer_individuals_profiles', function (Blueprint $table) {
            $table->bigIncrements('id'); // PK

            $table->unsignedBigInteger('tenant_id'); // FK -> tenants.id
            $table->unsignedBigInteger('customer_id'); // FK -> customers.id

            $table->string('first_name', 80);
            $table->string('middle_name', 80)->nullable();
            $table->string('last_name', 80);
            $table->string('full_name', 160)->virtualAs("concat(first_name, ' ', last_name)"); 
            // optional computed column

            $table->date('dob')->nullable();

            $table->enum('gender', ['male', 'female', 'other', 'unspecified'])->nullable();
            $table->string('avatar_url', 500)->nullable(); 
            $table->string('occupation', 120)->nullable();
            $table->string('nationality', 80)->nullable();

            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed', 'other'])->nullable();
            $table->string('preferred_language', 10)->nullable(); 
            $table->char('preferred_currency', 3)->nullable(); 

            $table->unsignedInteger('row_version')->default(0); // optimistic lock

            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at', 6)->nullable(); // soft delete
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->unique(['tenant_id', 'customer_id']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_individuals_profiles');
    }
};
