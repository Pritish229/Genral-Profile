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
        Schema::create('course_fees', function (Blueprint $table) {
            $table->id();
            $table->string('tenet_id')->nullable();
            $table->string('tenet_name')->nullable();
            $table->string('university_id')->nullable();
            $table->string('university_name')->nullable();
            $table->unsignedBigInteger('session_year_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('course_class_id');
            $table->integer('times_in_year');
            $table->unsignedBigInteger('fee_master_id');
            $table->string('fee_name');
            $table->string('fee_amount');
            $table->string('total_fee');
            $table->enum('feestype', ['1', '2', '3'])->default('1')->comment('0: Annual , 1: Monthly , 2: Other');
            $table->enum('status', ['0', '1',])->default('1')->comment('0: Active , 1: Inactive');
            $table->boolean('is_active')->default(true);
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('course_class_id')->references('id')->on('course_classes')->onDelete('cascade');
            $table->foreign('fee_master_id')->references('id')->on('fee_masters')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_fees');
    }
};
