<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Services\DocumentProcessorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        // Procesar documento
        $extracted = $processor->extractData($request->file);

        // Validación automática
        $errors = [];

        if (!isset($extracted['nit'])) {
            $errors['nit'] = 'NIT no detectado';
        }

        if (!isset($extracted['amount'])) {
            $errors['amount'] = 'Valor no detectado';
        }

        // Guardar archivo
        $filePath = $request->file->store('documents');

        // Si hay errores → rejected
        if (!empty($errors)) {

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

        // Guardado si es correcto
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

        // Notificación éxito
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
