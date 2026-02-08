<?php

// Define el namespace del modelo dentro de la estructura de Laravel.
// Los modelos representan tablas en la base de datos utilizando Eloquent ORM.
namespace App\Models;

// Importa la clase base Model de Eloquent.
use Illuminate\Database\Eloquent\Model;

// Modelo AuditLog que representa la tabla audit_logs en la base de datos.
// Se utiliza para registrar acciones realizadas sobre documentos
// (auditoría del sistema).
class AuditLog extends Model
{
    // Indica que el modelo NO usa timestamps automáticos (created_at y updated_at).
    // Laravel no intentará llenar estos campos automáticamente.
    public $timestamps = false;

    // Define los campos que pueden asignarse de forma masiva (mass assignment).
    // Esto permite usar métodos como AuditLog::create().
    protected $fillable = [
        'document_id', // ID del documento relacionado con la acción
        'action',      // Tipo de acción realizada (create, update, delete, etc.)
        'user_id',     // Usuario que ejecutó la acción
        'ip_address',  // Dirección IP desde donde se realizó la acción
        'user_agent',  // Información del navegador o cliente
        'changes',     // Detalle de los cambios realizados
    ];

    // Define conversiones automáticas de tipos (casting).
    // El campo 'changes' se convierte automáticamente a array
    // cuando se obtiene desde la base de datos.
    protected $casts = [
        'changes' => 'array'
    ];
}
