<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerPaymentAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',        
        'customer_id',
        'profile_type',
        'business_id',
        'business_name',
        'is_default_payout',
        'method',
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

    protected $casts = [
        'meta' => 'array',
        'is_primary' => 'boolean',
        'is_default_payout' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
