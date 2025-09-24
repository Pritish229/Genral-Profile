<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorPaymentAccount extends Model
{
    protected $fillable = [
        'tenant_id',        // ✅ Add this
        'vendor_id',
        'method',
        'account_holder',
        'bank_name',
        'branch_name',
        'ifsc_code',
        'swift_code',
        'upi_vpa',
        'is_primary',
        'status',
    ];
    
}
