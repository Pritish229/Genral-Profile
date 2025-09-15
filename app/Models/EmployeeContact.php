<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeContact extends Model
{
    use HasFactory, SoftDeletes;
     protected $table = 'employee_contacts';
     protected $fillable = [
        'tenant_id',
        'employee_id',
        'contact_type',
        'value',
        'normalized_value',
        'country_code',
        'label',
        'is_primary',
        'is_emergency',
        'verified',
        'verified_at',
        'verification_method',
        'source',
        'row_version',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'is_primary' => 'boolean',
        'is_emergency' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
