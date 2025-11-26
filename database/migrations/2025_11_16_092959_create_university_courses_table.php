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
        Schema::create('university_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('university_id');
            $table->foreign('university_id')->references('id')->on('universities')->onDelete('cascade');
            $table->unsignedBigInteger('course_id');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->string('course_name');
            $table->string('course_code')->nullable();
            $table->unsignedBigInteger('parent_course_id')->nullable();
            $table->foreign('parent_course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->string('parent_course_name')->nullable();
            $table->integer('duration_in_years')->references('course_duration')->on('courses')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_parent')->default(false);
            $table->timestamps();
            $table->timestamp('deleted_at', 6)->nullable();
            $table->unique(['university_id', 'course_id'], 'university_course_unique');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('university_courses');
    }
};
