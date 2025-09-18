<?php

namespace App\Models;

use App\Models\CustomerBusinessProfile;
use Illuminate\Database\Eloquent\Model;
use App\Models\CustomerIndividualProfile;
use App\Models\CustomerIndividualsProfile;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    protected $table = 'customers';
    protected $fillable = [
        'tenant_id',
        'customer_uid',
        'type',
        'primary_email',
        'primary_phone',
        'status',
        'registration_channel',
        'loyalty_id',
        'referral_code',
        'referred_by',
        'last_login_at',
        'last_order_id',
        'total_orders',
        'total_spent',
        'notes',
        'row_version',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'total_spent'   => 'decimal:2',
    ];



    public function isActive(): bool
    {
        return $this->status === 'active';
    }

     public function businessProfile()
    {
        return $this->hasOne(CustomerBusinessProfile::class);
    }

    public function individualProfile()
    {
        return $this->hasOne(CustomerIndividualProfile::class);
    }


    public function fullContact(): string
    {
        return $this->primary_email ?? $this->primary_phone ?? 'N/A';
    }
}
