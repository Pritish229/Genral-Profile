<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniversityCourse extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'university_courses';

    protected $fillable = [
        'university_id',
        'course_id',
        'parent_course_id',
        'course_name',
        'course_code',
        'parent_course_id',
        'duration_in_years',
        'total_semesters',
        'parent_name',
        'is_parent',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function university()
    {
        return $this->belongsTo(University::class, 'university_id');
    }

    /**
     * Main Course Relationship
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }



    /**
     * Parent Course Relationship
     */
    public function parentCourse()
    {
        return $this->belongsTo(Course::class, 'parent_course_id');
    }
}
