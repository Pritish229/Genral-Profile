<?php

namespace App\Models\Fee;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CollegeCourseFee extends Model
{
    use SoftDeletes;

    protected $table = 'college_course_fee';

    protected $fillable = [
        'college_id',
        'course_id',
        'course_name',
        'parent_course_id',
        'parent_course_name',
        'is_parent',
        'course_code',
        'duration_in_years',
        'fee_id',
        'fee_head',
        'fee_type',
        'collection_type',
        'times_in_year',
        'amount',
        'total_amount',
        'session_name',
        'session_start_date',
        'session_end_date',
    ];
}
