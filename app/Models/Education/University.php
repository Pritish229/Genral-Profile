<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class University extends Model
{
   use HasFactory, SoftDeletes;
   protected $table = 'universities';

   protected $fillable = [
      'org_name',
      'city',
      'district',
      'state',
      'phone_no',
      'alternate_no',
      'email_id',
      'alt_email_id',
      'org_logo',
      'address',
      'website_url'
   ];
   public function courses()
   {
      return $this->belongsToMany(Course::class, 'university_courses')
         ->withTimestamps();
   }
}
