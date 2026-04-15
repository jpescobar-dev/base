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
            $table->foreignId('revision_contractual_id')
                ->constrained('revisiones_contractuales')
                ->cascadeOnDelete();

            $table->string('nombre_original');
            $table->string('nombre_archivo');
            $table->string('ruta');
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('tamano')->nullable();
            $table->string('extension', 20)->nullable();
            $table->string('tipo_documento', 100)->nullable();
            $table->string('hash_archivo', 64)->nullable();
            $table->boolean('es_vigente')->default(true);
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();

            $table->index('revision_contractual_id', 'idx_doc_rev_contractual_revision_id');
            $table->index('tipo_documento', 'idx_doc_rev_contractual_tipo_documento');
            $table->index('es_vigente', 'idx_doc_rev_contractual_es_vigente');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_revision_contractual');
    }
};