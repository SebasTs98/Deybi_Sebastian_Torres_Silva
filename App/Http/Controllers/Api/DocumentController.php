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
     */
    public function index()
    {
        $documents = Document::latest()->get();

        return response()->json([
            'status' => 'success',
            'message' => 'API funcionando correctamente',
            'data' => $documents
        ]);
    }


    /**
     * POST /api/documents
     * Radicar documento
     */
    public function store(Request $request, DocumentProcessorService $processor)
    {

        $validator = Validator::make($request->all(), [
            'filing_number' => 'required|unique:documents',
            'document_type' => 'required|in:contractor_invoice,supplier_invoice,general_invoice',
            'email_recipient' => 'required|email',
            'file' => 'required|file|mimes:pdf,xml'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => 'error',
                'validation_errors' => $validator->errors()
            ], 422);

        }

        // Obtener archivo
        $file = $request->file('file');

        // Crear documento inicialmente en estado processing
        $document = Document::create([
            'filing_number' => $request->filing_number,
            'document_type' => $request->document_type,
            'email_recipient' => $request->email_recipient,
            'original_filename' => $file->getClientOriginalName(),
            'status' => 'processing',
        ]);

        try {

            // Procesar documento
            $extracted = $processor->extractData($file);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);

        }

        // Validación automática
        $errors = [];

        if (!isset($extracted['nit'])) {
            $errors['nit'] = 'NIT no detectado';
        }

        if (!isset($extracted['amount'])) {
            $errors['amount'] = 'Valor no detectado';
        }

        // Guardar archivo
        $filePath = $file->store('documents');

        $document->update([
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        // Si hay errores
        if (!empty($errors)) {

            $document->update([
                'extracted_data' => $extracted,
                'validation_errors' => $errors,
                'status' => 'rejected'
            ]);

            Notification::create([
                'document_id' => $document->id,
                'type' => 'error',
                'recipient_email' => $document->email_recipient,
                'subject' => 'Errores en radicación de documento',
                'body' => json_encode($errors)
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Documento rechazado por validación',
                'errors' => $errors
            ], 422);

        }

        // Si es correcto
        $document->update([
            'extracted_data' => $extracted,
            'status' => 'validated',
            'filed_at' => now()
        ]);

        // Auditoría
        AuditLog::create([
            'document_id' => $document->id,
            'action' => 'Documento radicado y validado',
            'user_id' => null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'changes' => $extracted
        ]);

        Notification::create([
            'document_id' => $document->id,
            'type' => 'success',
            'recipient_email' => $document->email_recipient,
            'subject' => 'Radicación exitosa',
            'body' => 'Su documento fue radicado correctamente.'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Documento radicado exitosamente',
            'data' => $document
        ]);

    }

}
