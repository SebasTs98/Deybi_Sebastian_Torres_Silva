<?php

// Define el namespace del modelo dentro de la estructura de Laravel.
namespace App\Models;

// Importa la clase base Model de Eloquent ORM.
use Illuminate\Database\Eloquent\Model;

// Importa el trait SoftDeletes que permite eliminaciones lógicas
// (soft delete) en lugar de borrar registros físicamente.
use Illuminate\Database\Eloquent\SoftDeletes;

// Modelo Document que representa la tabla documents en la base de datos.
// Gestiona la información relacionada con documentos radicados y procesados.
class Document extends Model
{
    // Habilita Soft Deletes.
    // Los registros eliminados se marcan con deleted_at en lugar de eliminarse definitivamente.
    use SoftDeletes;

    // Define los campos que pueden asignarse masivamente (mass assignment).
    // Permite usar métodos como Document::create().
    protected $fillable = [
        'filing_number',      // Número de radicación del documento
        'document_type',      // Tipo de documento
        'status',             // Estado del documento (ej: pendiente, procesado, validado)
        'original_filename',  // Nombre original del archivo cargado
        'file_path',          // Ruta donde se guarda el archivo
        'file_size',          // Tamaño del archivo
        'mime_type',          // Tipo MIME del archivo
        'extracted_data',     // Datos extraídos automáticamente del documento
        'validation_errors',  // Errores encontrados durante validación
        'metadata',           // Información adicional del documento
        'email_recipient',    // Correo del destinatario para notificaciones
        'filed_at',           // Fecha de radicación
        'processed_at',       // Fecha de procesamiento
        'validated_at'        // Fecha de validación
    ];

    // Define conversiones automáticas de tipos (casting).
    // Estos campos se convierten automáticamente en arrays al acceder a ellos.
    protected $casts = [
        'extracted_data' => 'array',
        'validation_errors' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Relación uno a muchos con AuditLog.
     * Un documento puede tener múltiples registros de auditoría.
     */
    public function logs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Relación uno a muchos con Notification.
     * Un documento puede tener múltiples notificaciones asociadas.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
