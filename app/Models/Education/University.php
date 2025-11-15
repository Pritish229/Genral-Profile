<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class University extends Model
{
    use HasFactory , SoftDeletes;
    protected $table = 'universities';

     protected $fillable = [];
}
