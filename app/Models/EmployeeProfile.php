<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeProfile extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'employee_profiles';

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'first_name',
        'middle_name',
        'last_name',
        'full_name',
        'dob',
        'gender',
        'blood_group',
        'avatar_url',
        'manager_id',
        'designation',
        'department',
        'employment_type',
        'salary_currency',
        'base_salary',
        'experience_years',
        'skills',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_relation',
        'row_version',
    ];

    protected $casts = [
        'dob' => 'date',
        'skills' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    protected static function booted()
    {
        static::saving(function ($profile) {
            $profile->full_name = trim(preg_replace(
                '/\s+/',
                ' ',
                ($profile->first_name ?? '') . ' ' .
                    (($profile->middle_name ?? '') ? ($profile->middle_name . ' ') : '') .
                    ($profile->last_name ?? '')
            ));
        });
    }
}
