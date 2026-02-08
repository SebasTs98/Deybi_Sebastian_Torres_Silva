<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DocumentController;

/*
|--------------------------------------------------------------------------
| API Routes - Document Management
|--------------------------------------------------------------------------
|
| Endpoints para gestión de documentos:
| - Obtener listado de documentos
| - Crear nuevos documentos
|
*/

/**
 * GET /api/documents
 * Obtiene la lista de documentos registrados.
 */
Route::get('/documents', [DocumentController::class, 'index']);

/**
 * POST /api/documents
 * Crea un nuevo documento en el sistema.
 *
 * Body esperado:
 * - filing_number
 * - document_type
 * - email_recipient
 */
Route::post('/documents', [DocumentController::class, 'store']);

