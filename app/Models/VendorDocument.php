<?php

namespace App\Models;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorDocument extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'tenant_id',
        'vendor_id',
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

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
