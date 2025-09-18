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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable(); // Multi-tenant scope

            $table->string('employee_uid', 50);
            $table->string('primary_email', 150)->nullable()->index();
            $table->string('primary_phone', 30)->nullable()->index();

            $table->enum('status', ['active', 'inactive', 'terminated', 'on_leave', 'retired'])
                ->default('active'); // Lifecycle

            $table->string('hire_date', 50)->nullable();
            $table->string('termination_date', 50)->nullable();

            $table->dateTime('last_login_at', 6)->nullable(); // For portal access
            $table->text('notes')->nullable(); // Internal remarks

            $table->unsignedInteger('row_version')->default(0); // Optimistic lock

            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at', 6)->nullable(); // Soft delete

            $table->unique(['tenant_id', 'employee_uid'], 'uq_employees_tenant_uid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
