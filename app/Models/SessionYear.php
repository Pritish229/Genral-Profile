<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SessionYear extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'session_years';
    protected $fillable = [
        'tenet_id',
        'emp_id',
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];
}
