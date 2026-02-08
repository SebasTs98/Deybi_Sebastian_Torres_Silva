<?php

// Define el namespace del controlador dentro de la API.
namespace App\Http\Controllers\Api;

// Importa el controlador base de Laravel.
use App\Http\Controllers\Controller;

// Importa modelos utilizados para interactuar con la base de datos.
use App\Models\Document;
use App\Models\AuditLog;
use App\Models\Notification;

// Importa el servicio encargado de procesar documentos (PDF/XML).
use App\Services\DocumentProcessorService;

// Importa clases necesarias para manejar requests y validaciones.
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// Controlador API encargado de gestionar la radicación y consulta de documentos.
class DocumentController extends Controller
{

    /**
     * GET /api/documents
     * Listar documentos
     *
     * Obtiene todos los documentos ordenados por fecha (últimos primero)
     * y retorna una respuesta JSON.
     */
    public function index()
    {
        // Obtiene todos los documentos ordenados por fecha descendente.
        $documents = Document::latest()->get();

        // Retorna respuesta JSON con estado, mensaje y datos.
        return response()->json([
            'status' => 'success',
            'message' => 'API funcionando correctamente',
            'data' => $documents
        ]);
    }


    /**
     * POST /api/documents
     * Radicar documento
     *
     * Valida el request, procesa el archivo (PDF/XML),
     * extrae información, guarda el documento, registra auditoría
     * y envía notificaciones según el resultado.
     */
    public function store(Request $request, DocumentProcessorService $processor)
    {

        // Validación de datos entrantes.
        $validator = Validator::make($request->all(), [
            'filing_number' => 'required|unique:documents', // Número de radicación único
            'document_type' => 'required|in:contractor_invoice,supplier_invoice,general_invoice', // Tipo permitido
            'email_recipient' => 'required|email', // Email válido
            'file' => 'required|file|mimes:pdf,xml' // Archivo obligatorio (pdf o xml)
        ]);

        // Si la validación falla retorna errores.
        if ($validator->fails()) {

            return response()->json([
                'status' => 'error',
                'validation_errors' => $validator->errors()
            ], 422);

        }

        // Procesa el documento utilizando el servicio.
        $extracted = $processor->extractData($request->file);

        // Validación automática de datos extraídos.
        $errors = [];

        // Verifica si el NIT fue detectado.
        if (!isset($extracted['nit'])) {
            $errors['nit'] = 'NIT no detectado';
        }

        // Verifica si el valor fue detectado.
        if (!isset($extracted['amount'])) {
            $errors['amount'] = 'Valor no detectado';
        }

        // Guarda físicamente el archivo en storage/documents.
        $filePath = $request->file->store('documents');

        // Si existen errores de validación automática → estado rejected.
        if (!empty($errors)) {

            // Crea el documento con estado rechazado.
            $document = Document::create([
                'filing_number' => $request->filing_number,
                'document_type' => $request->document_type,
                'email_recipient' => $request->email_recipient,
                'original_filename' => $request->file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $request->file->getSize(),
                'mime_type' => $request->file->getMimeType(),
                'extracted_data' => $extracted,
                'validation_errors' => $errors,
                'status' => 'rejected'
            ]);

            // Registra notificación de error.
            Notification::create([
                'document_id' => $document->id,
                'type' => 'error',
                'recipient_email' => $document->email_recipient,
                'subject' => 'Errores en radicación de documento',
                'body' => json_encode($errors)
            ]);

            // Retorna respuesta indicando rechazo.
            return response()->json([
                'status' => 'error',
                'message' => 'Documento rechazado por validación',
                'errors' => $errors
            ], 422);

        }

        // Si no hay errores → guarda documento validado.
        $document = Document::create([
            'filing_number' => $request->filing_number,
            'document_type' => $request->document_type,
            'email_recipient' => $request->email_recipient,
            'original_filename' => $request->file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_size' => $request->file->getSize(),
            'mime_type' => $request->file->getMimeType(),
            'extracted_data' => $extracted,
            'status' => 'validated',
            'filed_at' => now() // Fecha de radicación
        ]);

        // Registro de auditoría del proceso.
        AuditLog::create([
            'document_id' => $document->id,
            'action' => 'Documento radicado y validado',
            'user_id' => null,
            'ip_address' => $request->ip(), // IP del cliente
            'user_agent' => $request->userAgent(), // Información del navegador
            'changes' => $extracted // Datos extraídos
        ]);

        // Registro de notificación de éxito.
        Notification::create([
            'document_id' => $document->id,
            'type' => 'success',
            'recipient_email' => $document->email_recipient,
            'subject' => 'Radicación exitosa',
            'body' => 'Su documento fue radicado correctamente.'
        ]);

        // Respuesta final exitosa.
        return response()->json([
            'status' => 'success',
            'message' => 'Documento radicado exitosamente',
            'data' => $document
        ]);

    }

}
