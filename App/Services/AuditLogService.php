<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditLogService
{
    public function log($documentId, $action, $changes = null)
    {
        AuditLog::create([
            'document_id' => $documentId,
            'action' => $action,
            'changes' => $changes
        ]);
    }
}
