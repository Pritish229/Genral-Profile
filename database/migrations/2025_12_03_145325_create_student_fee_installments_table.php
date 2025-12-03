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
        Schema::create('student_fee_installments', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->unsignedBigInteger('college_id');
            $table->unsignedBigInteger('course_id');
            $table->string('course_name')->nullable();
            $table->string('course_code')->nullable();
            $table->unsignedBigInteger('parent_course_id')->nullable();
            $table->string('parent_course_name')->nullable();
            $table->boolean('is_parent')->default(0);
            $table->unsignedBigInteger('student_id');
            $table->string('student_name')->nullable();
            $table->string('session_year_name', 50);
            $table->decimal('total_due', 10, 2)->nullable();
            $table->decimal('installment_amount', 10, 2)->nullable();
            $table->decimal('balance_due', 10, 2)->nullable();
            $table->integer('installment_no')->nullable();
            $table->string('installment_title')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamps();

            // ⭐ SHORT INDEX NAMES FIX
            $table->index(['student_id', 'course_id', 'session_year_name'], 'fee_inst_student_course_session_idx');
            $table->index(['college_id', 'course_id'], 'fee_inst_college_course_idx');
            $table->index('parent_course_id', 'fee_inst_parent_idx');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('student_fee_installments');
    }
};
