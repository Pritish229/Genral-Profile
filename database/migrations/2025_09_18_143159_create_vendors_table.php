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
        Schema::create('vendors', function (Blueprint $table) {
            $table->bigIncrements('id'); // PK

            $table->unsignedBigInteger('tenant_id'); // FK -> tenants.id
            $table->string('vendor_uid', 50); // Unique external id

            $table->enum('type', ['individual', 'business'])->default('business'); // Vendor persona
            $table->string('primary_email', 150)->nullable()->index();
            $table->string('primary_phone', 30)->nullable()->index(); // E.164 format

            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active'); // lifecycle
            $table->enum('onboarding_channel', ['web', 'mobile', 'partner', 'import', 'other'])->nullable();

            $table->unsignedBigInteger('last_order_id')->nullable(); // optional FK shortcut
            $table->unsignedInteger('total_orders')->default(0); // cached
            $table->decimal('total_payout', 14, 2)->default(0); // lifetime payouts
            $table->text('notes')->nullable(); // internal
            $table->unsignedInteger('row_version')->default(0); // optimistic lock
            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at', 6)->nullable(); // soft delete
             $table->unique(['tenant_id', 'vendor_uid'], 'uq_vendors_tenant_uid');
        });

      
     
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
