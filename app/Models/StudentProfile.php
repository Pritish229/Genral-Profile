<?php

namespace App\Models;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentProfile extends Model
{
     use HasFactory, SoftDeletes;
     protected $table = 'student_profiles';
     
     protected $fillable = [
        'tenant_id',
        'student_id',
        'first_name',
        'middle_name',
        'last_name',
        'full_name',
        'dob',
        'gender',
        'blood_group',
        'religion',
        'caste',
        'nationality',
        'mother_tongue',
        'avatar_url',
        'guardian_name',
        'guardian_relation',
        'guardian_phone',
        'guardian_email',
        'guardian_occupation',
        'parent_income',
        'current_class',
        'section',
        'roll_no',
        'enrollment_status',
        'scholarship_status',
        'extracurriculars',
        'row_version',
    ];

    protected $casts = [
        'dob' => 'date',
        'parent_income' => 'decimal:2',
        'extracurriculars' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
