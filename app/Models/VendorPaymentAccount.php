<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorPaymentAccount extends Model
{
    protected $fillable = [
        'tenant_id',        
        'vendor_id',
        'profile_type',
        'business_id',
        'is_default_payout',
        'business_name',
        'method',
        'account_type',
        'account_holder',
        'bank_name',
        'account_number',
        'account_number_mask',
        'account_number_hash',
        'branch_name',
        'ifsc_code',
        'swift_code',
        'upi_vpa',
        'is_primary',
        'status',
    ];
}
