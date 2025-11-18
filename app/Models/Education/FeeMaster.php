<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeeMaster extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fee_masters';

    protected $fillable = [
        'fee_name',
        'fee_type',
        'status',
    ];
}
