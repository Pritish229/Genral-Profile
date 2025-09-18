<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeePaymentAccount extends Model
{
   use SoftDeletes;

    protected $table = 'employee_payment_accounts';

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'method',
        'status',
        'is_primary',
        'is_default_payout',
        'account_holder',
        'bank_name',
        'branch_name',
        'ifsc_code',
        'swift_code',
        'account_number_mask',
        'account_number_hash',
        'upi_vpa',
        'upi_verified',
        'verified',
        'verified_at',
        'verification_method',
        'source',
        'meta',
        'row_version',
    ];

    protected $casts = [
        'meta' => 'array',
        'is_primary' => 'boolean',
        'is_default_payout' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
