<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorOnileProfile extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'vendor_onile_profiles';

    protected $fillable = [
        'tenant_id',
        'vendor_id',
        'business_id',
        'business_name',
        'profile_type',
        'social_platform',
        'icon',
        'profile_url',
    ];
}
