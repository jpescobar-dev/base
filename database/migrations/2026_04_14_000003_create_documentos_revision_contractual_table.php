<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_revision_contractual', function (Blueprint $table) {

            $table->id();

            // Relación
            $table->foreignId('revision_contractual_id')
                ->constrained('revisiones_contractuales')
                ->cascadeOnDelete();

            // Datos del archivo
            $table->string('nombre_original');
            $table->string('ruta');
            $table->string('mime_type')->nullable();
            $table->string('extension', 10)->nullable();
            $table->bigInteger('peso_bytes')->nullable(); // ✅ NUEVO

            // Control de duplicados
            $table->string('hash_archivo', 64)->unique();

            // Clasificación documental
            $table->string('tipo_documento')->nullable();

            // Extracción de texto
            $table->longText('texto_extraido')->nullable();
            $table->longText('texto_ocr')->nullable(); // ✅ OCR

            // Estados de procesamiento
            $table->string('extraccion_estado', 50)->nullable();
            $table->string('ocr_estado', 50)->nullable();

            // Indicadores
            $table->boolean('tiene_texto_extraible')->default(false);
            $table->string('fuente_texto', 20)->nullable(); // texto | ocr

            // Auditoría
            $table->foreignId('user_id')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Índices útiles
            $table->index('revision_contractual_id');
            $table->index('tipo_documento');
            $table->index('extraccion_estado');
            $table->index('ocr_estado');
            $table->index('fuente_texto');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_revision_contractual');
    }
};