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
        'course_id',
        'name',
        'class_code',
        'description',
        'is_active',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'course_class_id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'course_class_id');
    }
}
