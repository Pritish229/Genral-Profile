<?php

namespace App\Models;

use App\Models\VendorBusinessProfile;
use App\Models\VendorIndividualProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'vendor_uid',
        'type',
        'primary_email',
        'primary_phone',
        'status',
        'onboarding_channel',
        'last_order_id',
        'total_orders',
        'total_payout',
        'notes',
        'row_version',
    ];

    protected $casts = [
        'total_payout' => 'decimal:2',
    ];


  

    public function businessProfile()
    {
        return $this->hasOne(VendorBusinessProfile::class);
    }

    public function individualProfile()
    {
        return $this->hasOne(VendorIndividualProfile::class);
    }

  

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function contactInfo(): string
    {
        return $this->primary_email ?? $this->primary_phone ?? 'N/A';
    }
}
