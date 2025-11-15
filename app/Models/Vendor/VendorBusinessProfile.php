<?php

namespace App\Models\Vendor;

use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorBusinessProfile extends Model
{
    use SoftDeletes;
    protected $table = 'vendor_business_profiles';

    protected $fillable = [
        'tenant_id',
        'vendor_id',
        'legal_name',
        'trade_name',
        'industry',
        'incorporation_date',
        'business_size',
        'website',
        'primary_contact_name',
        'primary_contact_email',
        'primary_contact_phone',
        'billing_email',
        'billing_phone',
        'gst_number',
        'pan_number',
        'cin_number',
        'credit_limit',
        'payment_terms_days',
        'account_manager',
        'row_version',
    ];

 

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
