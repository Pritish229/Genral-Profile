<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'college_id',
        'course_id',
        'course_name',
        'course_code',
        'parent_course_id',
        'parent_course_name',
        'duration_in_years',
        'starting_time',
        'ending_time',
        'is_parent',
        'is_active'
    ];

    public function university()
    {
        return $this->belongsTo(University::class, 'university_id');
    }

    public function college()
    {
        return $this->belongsTo(UniversityCollege::class, 'college_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function parentCourse()
    {
        return $this->belongsTo(Course::class, 'parent_course_id');
    }
}
