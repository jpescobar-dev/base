<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documentos_revision_contractual', function (Blueprint $table) {
            $table->longText('texto_extraido')->nullable()->after('hash_archivo');
            $table->string('extraccion_estado', 30)->default('PENDIENTE')->after('texto_extraido');
            $table->boolean('tiene_texto_extraible')->default(false)->after('extraccion_estado');

            $table->index('extraccion_estado', 'idx_doc_rev_extraccion_estado');
            $table->index('tiene_texto_extraible', 'idx_doc_rev_tiene_texto');
        });
    }

    public function down(): void
    {
        Schema::table('documentos_revision_contractual', function (Blueprint $table) {
            $table->dropIndex('idx_doc_rev_extraccion_estado');
            $table->dropIndex('idx_doc_rev_tiene_texto');
            $table->dropColumn(['texto_extraido', 'extraccion_estado', 'tiene_texto_extraible']);
        });
    }
};
