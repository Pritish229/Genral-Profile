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
        Schema::create('student_courses', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('college_id');

            $table->unsignedBigInteger('course_id');
            $table->string('course_name');

            $table->unsignedBigInteger('parent_course_id')->nullable();
            $table->string('parent_course_name')->nullable();

            $table->boolean('is_parent')->default(0);

            $table->string('course_code')->nullable();
            $table->integer('duration_in_years')->nullable();

            $table->unsignedBigInteger('student_id');
            $table->string('student_name');

            $table->string('session_year_name')->nullable();
            $table->date('session_start')->nullable();
            $table->date('session_end')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_courses');
    }
};
