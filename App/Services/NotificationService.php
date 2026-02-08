<?php

// Define el namespace del servicio dentro de la aplicación.
// Organiza la lógica relacionada con notificaciones en App\Services.
namespace App\Services;

// Importa la fachada Mail de Laravel, que permite enviar correos electrónicos
// utilizando el sistema de mailing configurado en la aplicación.
use Illuminate\Support\Facades\Mail;

// Clase encargada de gestionar el envío de notificaciones por correo.
// Centraliza la lógica para enviar mensajes de éxito o error.
class NotificationService
{
    /**
     * Envía una notificación de éxito por correo electrónico.
     *
     * @param string $email   Correo del destinatario.
     * @param string $subject Asunto del correo.
     * @param string $body    Contenido del mensaje.
     *
     * Este método debería usar un Mailable de Laravel para enviar
     * un correo cuando una operación se completa correctamente.
     */
    public function sendSuccess($email, $subject, $body)
    {
        // Ejemplo:
        // Mail::to($email)->send(new SuccessNotificationMail($subject, $body));
    }

    /**
     * Envía una notificación de error por correo electrónico.
     *
     * @param string $email  Correo del destinatario.
     * @param array  $errors Lista de errores generados durante un proceso.
     *
     * Este método puede enviar un resumen de errores para informar
     * al usuario o administrador sobre fallos en el sistema.
     */
    public function sendError($email, $errors)
    {
        // Ejemplo:
        // Mail::to($email)->send(new ErrorNotificationMail($errors));
    }
}
