<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory , SoftDeletes;
    protected $table = 'courses';
    protected $fillable = [
        'tenet_id',
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
