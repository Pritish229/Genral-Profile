<?php
namespace App\Models\Education;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeeMaster extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'tenet_id',
        'tenet_name',
        'university_id',
        'university_name',
        'emp_id',
        'fee_name',
    ];

}