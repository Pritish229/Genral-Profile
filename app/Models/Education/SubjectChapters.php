<?php

namespace App\Models\Education;


use App\Models\Education\Course;
use App\Models\Education\Subject;
use App\Models\Education\CourseClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubjectChapters extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'subject_chapters';
    protected $fillable = [
        'tenet_id',
        'tenet_name',
        'university_id',
        'university_name',
        'emp_id',
        'session_year_id',
        'course_id',
        'course_class_id',
        'subject_id',
        'chapter_name',
    ];

    public function class()
    {
        return $this->belongsTo(CourseClass::class, 'course_class_id');
    }
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
    public function sessionYear()
    {
        return $this->belongsTo(SessionYear::class, 'session_year_id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
