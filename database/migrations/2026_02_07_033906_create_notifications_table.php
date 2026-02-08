<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('document_id');
            $table->enum('type', ['success', 'error', 'info']);
            $table->string('recipient_email', 255);
            $table->string('subject', 255)->nullable();
            $table->text('body')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('error_message')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Relación
            $table->foreign('document_id')
                  ->references('id')
                  ->on('documents')
                  ->onDelete('cascade');

            // Índices
            $table->index('status');
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
}
