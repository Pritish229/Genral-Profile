<?php

namespace App\Models\Fee;

use Illuminate\Database\Eloquent\Model;


class StudentCourseFee extends Model
{
    protected $fillable = [
        'college_id',
        'course_id',
        'course_name',
        'course_code',
        'parent_course_id',
        'parent_course_name',
        'is_parent',
        'duration_in_years',
        'student_id',
        'student_name',
        'fee_id',
        'fee_head',
        'fee_type',
        'collection_type',
        'session_one_name',
        'session_one_amount',
        'session_two_name',
        'session_two_amount',
        'session_three_name',
        'session_three_amount',
        'session_four_name',
        'session_four_amount',
        'session_five_name',
        'session_five_amount',
    ];
}
