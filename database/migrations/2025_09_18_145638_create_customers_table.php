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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id'); // PK

            $table->unsignedBigInteger('tenant_id'); // FK -> tenants.id
            $table->string('customer_uid', 50); // Unique external/public identifier

            $table->enum('type', ['individual', 'business'])->default('individual'); // persona

            $table->string('primary_email', 150)->nullable()->index();
            $table->string('primary_phone', 30)->nullable()->index(); // E.164 format

            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active'); // lifecycle
            $table->enum('registration_channel', ['web', 'mobile', 'store', 'partner', 'other'])->nullable();

            $table->unsignedBigInteger('loyalty_id')->nullable(); // link to loyalty program
            $table->string('referral_code', 50)->nullable(); // assigned referral code
            $table->string('referred_by', 50)->nullable(); // referral source UID or code

            $table->dateTime('last_login_at', 6)->nullable(); // last successful login

            $table->unsignedBigInteger('last_order_id')->nullable(); // shortcut FK -> orders.id
            $table->unsignedInteger('total_orders')->default(0); // cached
            $table->decimal('total_spent', 14, 2)->default(0.00); // LTV

            $table->text('notes')->nullable(); // internal admin notes

            $table->unsignedInteger('row_version')->default(0); // optimistic lock

            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at', 6)->nullable(); // soft delete

            // Constraints
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->unique(['tenant_id', 'customer_uid']); // enforce uniqueness within tenant
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
