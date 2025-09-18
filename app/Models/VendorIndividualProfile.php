<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorIndividualProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'vendor_id',
        'first_name',
        'middle_name',
        'last_name',
        'dob',
        'gender',
        'avatar_url',
        'occupation',
        'nationality',
        'marital_status',
        'preferred_language',
        'preferred_currency',
        'row_version',
    ];

    protected $appends = ['full_name'];

    // Computed Attribute
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
 

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
