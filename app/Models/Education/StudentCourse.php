<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Model;

class StudentCourse extends Model
{
    protected $table = 'student_courses';

    protected $fillable = [
        'college_id',
        'course_id',
        'course_name',
        'parent_course_id',
        'parent_course_name',
        'is_parent',
        'course_code',
        'duration_in_years',
        'student_id',
        'student_name',
        'session_year_name',
        'session_start',
        'session_end'
    ];
}
