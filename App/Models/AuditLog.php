<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'document_id',
        'action',
        'user_id',
        'ip_address',
        'user_agent',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array'
    ];
}
