<?php

namespace App\Models;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentAddress extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'student_addresses';

    protected $fillable = [
        'tenant_id',
        'student_id',
        'address_type',
        'label',
        'line1',
        'line2',
        'landmark',
        'city',
        'district',
        'state',
        'country',
        'pincode',
        'latitude',
        'longitude',
        'is_primary',
        'is_verified',
        'verified_at',
        'row_version',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}