<?php

namespace App\Models\Education;

use App\Models\SessionYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'subjects';
    protected $fillable = [
        'session_year_id',
        'course_id',
        'course_class_id',
        'subject_name',
        'subject_code',
        'description',
        'is_active',
    ];

    public function class()
    {
        return $this->belongsTo(CourseClass::class, 'course_class_id');
    }
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
    
}
