<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerContact extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'tenant_id',
        'customer_id',
        'business_id',
        'business_name',
        'profile_type',
        'business_id',
        'business_name',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
