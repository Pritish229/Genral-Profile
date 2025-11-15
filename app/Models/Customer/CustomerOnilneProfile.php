<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerOnilneProfile extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'customer_onilne_profiles';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'business_id',
        'business_name',
        'profile_type',
        'social_platform',
        'icon',
        'profile_url',
    ];
}
