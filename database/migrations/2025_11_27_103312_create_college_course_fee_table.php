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
        Schema::create('college_course_fee', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('college_id')->nullable();
            $table->bigInteger('course_id')->nullable();
            $table->string('course_name', 200)->nullable();
            $table->bigInteger('parent_course_id')->nullable();
            $table->string('parent_course_name', 200)->nullable();
            $table->boolean('is_parent')->nullable();
            $table->string('course_code', 50)->nullable();
            $table->integer('duration_in_years')->nullable();
            $table->bigInteger('fee_id')->nullable();
            $table->string('fee_head', 150)->nullable();
            $table->string('fee_type', 50)->nullable();
            $table->enum('collection_type', ['optional', 'mandatory'])->default('mandatory')->nullable(); 
            $table->integer('times_in_year')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->string('session_name', 50)->nullable();
            $table->date('session_start_date')->nullable();
            $table->date('session_end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('college_course_fee');
    }
};
