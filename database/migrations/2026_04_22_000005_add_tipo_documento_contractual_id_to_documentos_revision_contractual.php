<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documentos_revision_contractual', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_documento_contractual_id')
                ->nullable()
                ->after('tipo_documento');

            $table->foreign('tipo_documento_contractual_id', 'fk_doc_tipo_contractual')
                ->references('id')
                ->on('tipos_documento_contractual')
                ->nullOnDelete();

            $table->index('tipo_documento_contractual_id', 'idx_doc_tipo_contractual');
        });
    }

    public function down(): void
    {
        Schema::table('documentos_revision_contractual', function (Blueprint $table) {
            $table->dropForeign('fk_doc_tipo_contractual');
            $table->dropIndex('idx_doc_tipo_contractual');
            $table->dropColumn('tipo_documento_contractual_id');
        });
    }
};
