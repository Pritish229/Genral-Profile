<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerAddress extends Model
{
     use HasFactory, SoftDeletes;

     protected $fillable = [
        'tenant_id',
        'customer_id',
        'address_type',
        'label',
        'line1',
        'line2',
        'landmark',
        'city',
        'district',
        'state',
        'country',
        'pincode',
        'latitude',
        'longitude',
        'is_primary',
        'is_verified',
        'verified_at',
        'row_version',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
