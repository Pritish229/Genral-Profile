<?php

namespace App\Models\Education;

use App\Models\Education\Course;
use App\Models\Education\FeeMaster;
use App\Models\Education\CourseClass;
use App\Models\Education\SessionYear;
use Illuminate\Database\Eloquent\Model;

class CourseFee extends Model
{
    protected $fillable = [
        'tenet_id',
        'tenet_name',
        'university_id',
        'university_name',
        'session_year_id',
        'course_id',
        'fee_master_id',
        'fee_name',
        'course_class_id',
        'times_in_year',
        'fee_amount',
        'total_fee',
        'feestype',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'times_in_year' => 'integer',
        'session_year_id' => 'integer',
        'course_id' => 'integer',
        'course_class_id' => 'integer'
    ];

    public function sessionYear()
    {
        return $this->belongsTo(SessionYear::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function feeMaster()
    {
        return $this->belongsTo(FeeMaster::class, 'fee_master_id');
    }
}
