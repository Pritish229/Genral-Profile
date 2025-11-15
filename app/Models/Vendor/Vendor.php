<?php

namespace App\Models\Vendor;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vendor\VendorBusinessProfile;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Vendor\VendorIndividualProfile;

class Vendor extends Model
{
    use SoftDeletes;

    protected $table = 'vendors';

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

    // ✅ Existing individualProfile relationship
    public function individualProfile()
    {
        return $this->hasOne(VendorIndividualProfile::class);
    }

    // ✅ Add this new businessProfile relationship
    public function businessProfile()
    {
        return $this->hasOne(VendorBusinessProfile::class, 'vendor_id');
    }

    protected static function booted()
    {
        static::creating(function ($vendor) {
            if (empty($vendor->vendor_uid)) {
                $vendor->vendor_uid = 'VEND-' . strtoupper(Str::random(8));
            }
        });
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