<?php

namespace App\Models\Education;

use App\Models\Education\CourseClass;
use App\Models\Education\SessionYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClassSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'class_sections';

    protected $fillable = [
        'tenet_id',
        'emp_id',
        'tenet_name',
        'university_id',
        'university_name',
        'session_year_id',
        'course_id',
        'course_class_id',
        'section_name',   
        'section_code',
        'description',
        'is_active',
    ];

    /**
     * ✅ Relationship: Session Year
     */
    public function sessionYear()
    {
        return $this->belongsTo(SessionYear::class, 'session_year_id');
    }

    /**
     * ✅ Relationship: Course
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * ✅ Relationship: Course Class
     */
    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class, 'course_class_id');
    }

    /**
     * ✅ Accessor: Active / Inactive Label (Optional Helper)
     */
    public function getStatusBadgeAttribute()
    {
        return $this->is_active
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-secondary">Inactive</span>';
    }
}
