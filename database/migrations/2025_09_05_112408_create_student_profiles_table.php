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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AI PK
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('student_id'); 

            $table->string('first_name', 80);
            $table->string('middle_name', 80)->nullable();
            $table->string('last_name', 80);
            $table->string('full_name', 160)->nullable(); // Can also be generated

            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'unspecified']);
            $table->string('blood_group', 5)->nullable();
            $table->string('religion', 60)->nullable();
            $table->string('caste', 60)->nullable();
            $table->string('nationality', 80)->nullable();
            $table->string('mother_tongue', 60)->nullable();

            $table->string('avatar_url', 500);

            // Guardian Info
            $table->string('guardian_name', 150)->nullable();
            $table->string('guardian_relation', 80)->nullable();
            $table->string('guardian_phone', 30)->nullable();
            $table->string('guardian_email', 150)->nullable();
            $table->string('guardian_occupation', 120)->nullable();
            $table->decimal('parent_income', 12, 2)->nullable();

            // Academic Info
            $table->string('current_class', 40);
            $table->string('section', 10)->nullable();
            $table->string('roll_no', 30)->nullable();
            $table->enum('enrollment_status', ['regular', 'transfer', 'provisional'])->default('regular');
            $table->enum('scholarship_status', ['none', 'applied', 'approved'])->default('none');

            // Extra
            $table->json('extracurriculars')->nullable();

            $table->unsignedInteger('row_version')->default(0);

            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at', 6)->nullable();

            // Indexes
            $table->unique(['tenant_id', 'student_id']); 
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
