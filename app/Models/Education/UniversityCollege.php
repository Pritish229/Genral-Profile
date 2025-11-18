<?php

namespace App\Models\Education;

use App\Models\Education\University;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UniversityCollege extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
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
        'website_url',
    ];

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    // NEW → This helps DataTables use a flat field
    public function getUniversityNameAttribute()
    {
        return $this->university?->org_name ?? 'N/A';
    }
}
