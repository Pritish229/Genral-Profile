<?php

namespace App\Models\Education;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SessionYear extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'session_years';
    protected $fillable = [
        'tenet_id',
        'tenet_name',
        'university_id',
        'university_name',
        'emp_id',
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];
}
