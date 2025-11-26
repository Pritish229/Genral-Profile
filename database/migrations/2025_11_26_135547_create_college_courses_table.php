<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('college_courses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('university_id')->constrained('universities')->onDelete('cascade');
            $table->foreignId('college_id')->constrained('university_colleges')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');

            $table->string('course_name');
            $table->string('course_code')->nullable();

            $table->foreignId('parent_course_id')->nullable()->constrained('courses')->onDelete('cascade');

            $table->string('parent_course_name')->nullable();
            $table->integer('duration_in_years')->nullable();

            $table->date('starting_date');
            $table->date('ending_date')->nullable();

            $table->boolean('is_parent')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('college_courses');
    }
};
