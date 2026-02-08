<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('filing_number', 50)->unique();
            $table->enum('document_type', ['contractor_invoice','supplier_invoice','general_invoice']);
            $table->enum('status', ['pending','processing','validated','rejected','approved','paid'])->default('pending');

            // Información del archivo
            $table->string('original_filename', 255);
            $table->string('file_path', 500);
            $table->integer('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();

            // Datos extraídos y validación
            $table->json('extracted_data')->nullable();
            $table->json('validation_errors')->nullable();

            // Metadatos
            $table->json('metadata')->nullable();
            $table->string('email_recipient', 255)->nullable();

            // Timestamps
            $table->timestamp('filed_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('filing_number');
            $table->index('status');
            $table->index('document_type');
            $table->index('filed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
}
