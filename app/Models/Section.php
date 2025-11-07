<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Section extends Model
{
    use HasFactory , SoftDeletes;
    protected $table = 'sections';
    protected $fillable = [
        'tenet_id',
        'emp_id',
        'course_class_id',
        'name',
        'section_code',
        'description',
        'is_active',
    ];

    public function class()
    {
        return $this->belongsTo(CourseClass::class, 'course_class_id');
    }
}
