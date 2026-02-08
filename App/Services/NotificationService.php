<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sendSuccess($email, $subject, $body)
    {
        // Mail::to($email)->send(...) aquí tu Mailable
    }

    public function sendError($email, $errors)
    {
        // Enviar correo con errores
    }
}
