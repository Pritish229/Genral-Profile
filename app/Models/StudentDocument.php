<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class StudentDocument extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'student_documents';
    protected $fillable = [
        'tenant_id',
        'student_id',
        'document_type',
        'document_number',
        'document_number_hash',
        'document_number_mask',
        'issue_date',
        'expiry_date',
        'issuing_authority',
        'file_name',
        'file_url',
        'file_type',
        'verified',
        'verified_at',
        'verified_by',
        'remarks',
        'source',
        'meta',
        'row_version',
    ];

    protected $casts = [
        'issue_date'   => 'date',
        'expiry_date'  => 'date',
        'verified_at'  => 'datetime',
        'meta'         => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
