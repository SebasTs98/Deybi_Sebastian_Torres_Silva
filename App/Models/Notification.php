<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'document_id',
        'type',
        'recipient_email',
        'subject',
        'body',
        'sent_at',
        'status',
        'error_message'
    ];
}
