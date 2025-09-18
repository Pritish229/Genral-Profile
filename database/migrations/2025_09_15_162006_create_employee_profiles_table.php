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
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('employee_id');

            $table->string('first_name', 80);
            $table->string('middle_name', 80)->nullable();
            $table->string('last_name', 80);
            $table->string('full_name', 160)->nullable();

            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'unspecified']);
            $table->string('blood_group', 5)->nullable();

            $table->string('avatar_url', 500)->nullable();
            $table->string('designation', 120)->nullable();
            $table->string('department', 120)->nullable();

            // Manager is another employee → FK
            $table->unsignedBigInteger('manager_id')->nullable();

            $table->string('location', 150)->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intern', 'consultant'])->nullable();
            $table->string('salary_currency', 3)->nullable();
            $table->decimal('base_salary', 12, 2)->nullable();
            $table->decimal('experience_years', 4, 1)->nullable();

            $table->json('skills')->nullable();

            $table->string('emergency_contact_name', 150)->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();
            $table->string('emergency_relation', 80)->nullable();

            $table->unsignedInteger('row_version')->default(0);

            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at', 6)->nullable();

            // 🔑 Constraints
            $table->unique(['tenant_id', 'employee_id']);
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('manager_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_profiles');
    }
};
