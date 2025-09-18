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
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('customer_id'); // FK → customers.id

            $table->enum('address_type', [
                'permanent',
                'other',
                'hostel',
            ])->default('permanent');

            $table->string('label', 120)->nullable();
            $table->string('line1', 180)->nullable();
            $table->string('line2', 180)->nullable();
            $table->string('landmark', 150)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('district', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('country', 100)->default('India');
            $table->string('pincode', 12)->nullable();

            // Geo support
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Flags
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->dateTime('verified_at', 6)->nullable();

            $table->unsignedInteger('row_version')->default(0);

            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at', 6)->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
