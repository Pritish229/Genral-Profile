<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_course_fees', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('college_id')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->string('course_name', 255)->nullable();
            $table->string('course_code', 50)->nullable();

            $table->unsignedBigInteger('parent_course_id')->nullable();
            $table->string('parent_course_name', 255)->nullable();

            $table->boolean('is_parent')->default(0);
            $table->integer('duration_in_years')->nullable();

            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_name', 255)->nullable();

            $table->unsignedBigInteger('fee_id')->nullable();
            $table->string('fee_head', 150)->nullable();
            $table->enum('fee_type',  ['0', '1'])->nullable();
            $table->enum('collection_type', ['optional','mandatory'])->default('optional');

            $table->string('session_one_name', 50)->nullable();
            $table->decimal('session_one_amount', 12, 2)->nullable();

            $table->string('session_two_name', 50)->nullable();
            $table->decimal('session_two_amount', 12, 2)->nullable();

            $table->string('session_three_name', 50)->nullable();
            $table->decimal('session_three_amount', 12, 2)->nullable();

            $table->string('session_four_name', 50)->nullable();
            $table->decimal('session_four_amount', 12, 2)->nullable();

            $table->string('session_five_name', 50)->nullable();
            $table->decimal('session_five_amount', 12, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_course_fees');
    }
};
