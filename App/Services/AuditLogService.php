<?php

// Define el namespace donde se encuentra el servicio.
// Permite organizar el código dentro de la carpeta App\Services.
namespace App\Services;

// Importa el modelo AuditLog para poder interactuar con la tabla de auditoría
// en la base de datos mediante Eloquent ORM.
use App\Models\AuditLog;

// Clase de servicio encargada de manejar la lógica relacionada
// con los registros de auditoría del sistema.
class AuditLogService
{
    /**
     * Registra una acción en el log de auditoría.
     *
     * @param int $documentId  ID del documento asociado a la acción.
     * @param string $action   Tipo de acción realizada (create, update, delete, etc.).
     * @param mixed $changes   Información adicional sobre cambios realizados (opcional).
     *
     * Esta función crea un nuevo registro en la tabla audit_logs
     * guardando la acción ejecutada sobre un documento.
     */
    public function log($documentId, $action, $changes = null)
    {
        // Crea un registro en la tabla audit_logs usando el modelo AuditLog.
        AuditLog::create([
            'document_id' => $documentId, // ID del documento afectado
            'action' => $action,          // Acción realizada
            'changes' => $changes         // Detalle de cambios (opcional)
        ]);
    }
}
