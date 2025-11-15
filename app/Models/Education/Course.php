<?php

namespace App\Models\Education;


use App\Models\SessionYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory , SoftDeletes;
    protected $table = 'courses';
    protected $fillable = [
        'tenet_id',
        'tenet_name',
        'university_id',
        'university_name',
        'emp_id',
        'session_year_id',
        'course_name',
        'course_code',
        'course_image',
        'is_active',
        'description',
    ];

    public function sessionYear()
    {
        return $this->belongsTo(SessionYear::class);
    }

   
}
