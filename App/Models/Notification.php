<?php

// Define el namespace del modelo dentro de la estructura de Laravel.
namespace App\Models;

// Importa la clase base Model de Eloquent ORM.
use Illuminate\Database\Eloquent\Model;

// Modelo Notification que representa la tabla notifications en la base de datos.
// Se utiliza para almacenar el historial de notificaciones enviadas
// relacionadas con documentos.
class Notification extends Model
{
    // Indica que el modelo no utiliza timestamps automáticos
    // (created_at y updated_at).
    public $timestamps = false;

    // Define los campos que pueden asignarse de forma masiva (mass assignment).
    // Permite crear registros usando Notification::create().
    protected $fillable = [
        'document_id',     // ID del documento asociado a la notificación
        'type',            // Tipo de notificación (ej: success, error, info)
        'recipient_email', // Correo electrónico del destinatario
        'subject',         // Asunto del correo enviado
        'body',            // Contenido del mensaje
        'sent_at',         // Fecha y hora en que se envió la notificación
        'status',          // Estado del envío (ej: enviado, fallido)
        'error_message'    // Mensaje de error en caso de fallo al enviar
    ];
}
