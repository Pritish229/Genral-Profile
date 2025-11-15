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
        Schema::create('class_sections', function (Blueprint $table) {
            $table->id();
            $table->string('tenet_id')->nullable();
            $table->string('tenet_name')->nullable();
            $table->string('university_id')->nullable();
            $table->string('university_name')->nullable();
            $table->string('emp_id')->nullable();
            $table->unsignedBigInteger('session_year_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('course_class_id');
            $table->string('section_name', 100);
            $table->string('section_code', 50)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->timestamp('deleted_at', 6)->nullable();
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('course_class_id')->references('id')->on('course_classes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_sections');
    }
};
