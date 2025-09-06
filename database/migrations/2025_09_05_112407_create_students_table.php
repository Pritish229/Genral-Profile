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
        Schema::create('students', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('tenant_id')->nullable(); // Multi-tenant scope

            $table->string('student_uid', 50);
            $table->string('primary_email', 150)->nullable()->index();
            $table->string('primary_phone', 30)->nullable()->index();

            $table->enum('status', ['active', 'inactive', 'alumni', 'transferred', 'blocked'])
                  ->default('active'); // Lifecycle

            $table->string('admission_no', 50)->nullable();
            $table->string('univ_admission_no', 50)->nullable();
            $table->date('admission_date')->nullable();

            $table->dateTime('last_login_at', 6)->nullable(); // For portal access
            $table->text('notes')->nullable(); // Internal remarks

            $table->unsignedInteger('row_version')->default(0); // Optimistic lock

            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at', 6)->nullable(); // Soft delete

            // 🔑 Composite Unique: student_uid per tenant
            $table->unique(['tenant_id', 'student_uid'], 'uq_students_tenant_uid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }

  
};
