<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorContact extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'tenant_id',
        'vendor_id',
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

    public function vendor()
    {
        return $this->belongsTo(vendor::class);
    }
}
