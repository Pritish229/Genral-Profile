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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('tenet_id')->nullable();
            $table->string('tenet_name')->nullable();
            $table->string('university_id')->nullable();
            $table->string('university_name')->nullable();
            $table->string('emp_id')->nullable();
            $table->foreignId('session_year_id')->constrained('session_years')->onDelete('cascade');
            $table->string('course_name');
            $table->string('course_code')->unique();
            $table->string('course_image')->nullable();
            $table->boolean('is_active')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at', 6)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
