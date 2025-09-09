<?php

namespace App\Models;

use App\Models\StudentMedia;
use App\Models\StudentAddress;
use App\Models\StudentContact;
use App\Models\StudentProfile;
use App\Models\StudentDocument;
use App\Models\StudentPaymentAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use SoftDeletes;

    protected $table = 'students';

    protected $fillable = [
        'tenant_id',
        'student_uid',
        'primary_email',
        'primary_phone',
        'status',
        'admission_no',
        'univ_admission_no',
        'admission_date',
        'last_login_at',
        'notes',
        'row_version',
    ];

    /**
     * Relationships
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (empty($student->student_uid)) {
                do {
                    $uid = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
                } while (self::where('student_uid', $uid)->exists());

                $student->student_uid = $uid;
            }
        });
    }
    public function paymentMethods()
    {
        return $this->hasMany(StudentPaymentAccount::class, 'student_id');
    }

    public function primaryPaymentMethod()
    {
        return $this->hasOne(StudentPaymentAccount::class, 'student_id')
            ->where('is_primary', true);
    }

    public function defaultPayoutMethod()
    {
        return $this->hasOne(StudentPaymentAccount::class, 'student_id')
            ->where('is_default_payout', true);
    }

    public function profile()
    {
        return $this->hasOne(StudentProfile::class, 'student_id', 'id');
    }
}
