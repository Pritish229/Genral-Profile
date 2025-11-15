<?php

namespace App\Models\Education;

use App\Models\SessionYear;
use App\Models\Education\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenet_id',
        'emp_id',
        'session_year_id',
        'course_id',
        'class_name',
        'class_code',
        'description',
        'is_active',
    ];

    // 🔗 Belongs to a Session Year
    public function sessionYear()
    {
        return $this->belongsTo(SessionYear::class, 'session_year_id');
    }

    // 🔗 Belongs to a Course
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    // 🔗 Has many Subjects
    public function subjects()
    {
        return $this->hasMany(Subject::class, 'course_class_id');
    }
}
