<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Model;
use App\Models\Education\UniversityCourse;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'courses';

    protected $fillable = [
        'course_name',
        'parent_id',
        'course_code',
        'is_parent',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_parent' => 'string',  // expected: 'true' or 'false'
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Course::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Course::class, 'parent_id');
    }

    public function universityCourses()
    {
        return $this->hasMany(UniversityCourse::class, 'university_id');
    }

    public function getIsParentAttribute()
    {
        return $this->children()->count() > 0 ? 'true' : 'false';
    }
}
