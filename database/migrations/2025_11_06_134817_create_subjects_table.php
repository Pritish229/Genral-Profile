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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('tenet_id')->nullable();
            $table->string('tenet_name')->nullable();
            $table->string('university_id')->nullable();
            $table->string('university_name')->nullable();
            $table->string('emp_id')->nullable();

            $table->foreignId('session_year_id')->constrained('session_years')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('course_class_id')->constrained('course_classes')->onDelete('cascade');

            $table->string('subject_name');
            $table->string('subject_code')->nullable();

            // 🆕 Newly added columns
            $table->enum('has_practicals', ['true', 'false'])->default('false'); // whether it has practicals
            $table->integer('theory_mark')->nullable(); // theory marks
            $table->integer('practical_mark')->nullable(); // practical marks
            $table->integer('full_mark'); // total marks

            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->timestamp('deleted_at', 6)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
