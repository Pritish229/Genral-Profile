<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentMedia extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'student_medias';

    protected $fillable = [
        'tenant_id',
        'student_id',
        'media_usage',
        'subject_role',
        'subject_name',
        'file_url',
        'file_name',
        'mime_type',
        'size_bytes',
        'width',
        'height',
        'duration_sec',
        'checksum_hash',
        'storage_provider',
        'storage_bucket',
        'storage_path',
        'alt_text',
        'caption',
        'tags',
        'thumbnails',
        'is_primary',
        'is_public',
        'status',
        'meta',
        'row_version',
    ];

    protected $casts = [
        'tags' => 'array',
        'thumbnails' => 'array',
        'meta' => 'array',
        'is_primary' => 'boolean',
        'is_public' => 'boolean',
        'size_bytes' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'duration_sec' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}