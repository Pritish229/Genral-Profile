<?php

namespace App\Models;

use App\Models\VendorBusinessProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorContact extends Model
{
    use SoftDeletes;

    protected $table = 'vendor_contacts';

    protected $fillable = [
        'tenant_id',
        'vendor_id',
        'business_id',
        'business_name',
        'profile_type',
        'department',
        'designation',
        'contact_person_type',     
        'contact_person_name', 
        'contact_person_type',    
        'contact_type',
        'value',
        'extension',               
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
        'is_primary'   => 'boolean',
        'is_emergency' => 'boolean',
        'verified_at'  => 'datetime',
    ];

    /**
     * Relationships
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function businessProfile() { return $this->hasOne(VendorBusinessProfile::class, 'vendor_id'); }

    /**
     * Accessor for full contact info
     */
    public function getContactDisplayAttribute(): string
    {
        $ext = $this->extension ? ' ext. ' . $this->extension : '';
        return "{$this->contact_type}: {$this->value}{$ext}";
    }
}
