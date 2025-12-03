<?php

namespace App\Models\Fee;

use App\Models\Student\Student;
use App\Models\Education\CollegeCourse;
use Illuminate\Database\Eloquent\Model;

class StudentFeeInstallment extends Model
{
    protected $table = 'student_fee_installments';

    protected $guarded = [];

    protected $casts = [
        'is_parent'          => 'boolean',
        'total_due'          => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'balance_due'        => 'decimal:2',
        'due_date'           => 'date'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(CollegeCourse::class, 'course_id');
    }

    public function parentCourse()
    {
        return $this->belongsTo(CollegeCourse::class, 'parent_course_id');
    }

    public function getIsChildAttribute(): bool
    {
        return !$this->is_parent;
    }

    public function getInstallmentLabelAttribute(): string
    {
        if ($this->is_parent) {
            return 'Total Summary';
        }

        return "Installment {$this->installment_no}";
    }
}
