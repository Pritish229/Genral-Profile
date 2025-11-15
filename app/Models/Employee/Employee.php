<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee\EmployeeProfile;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Employee\EmployeePaymentAccount;

class Employee extends Model
{
    use SoftDeletes;

    protected $table = 'employees';

    protected $fillable = [
        'tenant_id',
        'employee_uid',
        'primary_email',
        'primary_phone',
        'status',
        'hire_date',
        'termination_date',
        'last_login_at',
        'notes',
        'row_version',
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            if (empty($employee->employee_uid)) {
                do {
                    $uid = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
                } while (self::where('employee_uid', $uid)->exists());

                $employee->employee_uid = $uid;
            }
        });
    }
    public function paymentMethods()
    {
        return $this->hasMany(EmployeePaymentAccount::class, 'employee_id');
    }

    public function primaryPaymentMethod()
    {
        return $this->hasOne(EmployeePaymentAccount::class, 'employee_id')
            ->where('is_primary', true);
    }

    public function defaultPayoutMethod()
    {
        return $this->hasOne(EmployeePaymentAccount::class, 'employee_id')
            ->where('is_default_payout', true);
    }

    public function profile()
    {
        return $this->hasOne(EmployeeProfile::class, 'employee_id', 'id');
    }
}
