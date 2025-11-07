<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    use HasFactory;

    protected $table = 'course_classes';

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

    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function sessionYear()
    {
        return $this->belongsTo(SessionYear::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
