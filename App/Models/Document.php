<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'filing_number',
        'document_type',
        'status',
        'original_filename',
        'file_path',
        'file_size',
        'mime_type',
        'extracted_data',
        'validation_errors',
        'metadata',
        'email_recipient',
        'filed_at',
        'processed_at',
        'validated_at'
    ];

    protected $casts = [
        'extracted_data' => 'array',
        'validation_errors' => 'array',
        'metadata' => 'array',
    ];

    public function logs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}

