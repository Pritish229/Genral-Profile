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
        Schema::create('subject_chapters', function (Blueprint $table) {
            $table->id();
            $table->string('tenet_id')->nullable();
            $table->string('tenet_name')->nullable();
            $table->string('university_id')->nullable();
            $table->string('university_name')->nullable();
            $table->string('emp_id')->nullable();
            $table->foreignId('session_year_id')->constrained('session_years')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('course_class_id')->constrained('course_classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('chapter_name');
            $table->timestamp('deleted_at', 6)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_chapters');
    }
};
