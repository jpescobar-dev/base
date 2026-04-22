<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documento_snapshot_revision_contractual', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('snapshot_revision_contractual_id');
            $table->unsignedBigInteger('documento_revision_contractual_id');
            $table->string('fuente_texto_usada', 20)->nullable();
            $table->string('estado_extraccion', 50)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->unique(
                ['snapshot_revision_contractual_id', 'documento_revision_contractual_id'],
                'uq_doc_snapshot_revision'
            );

            $table->index('snapshot_revision_contractual_id', 'idx_doc_snapshot_snapshot');
            $table->index('documento_revision_contractual_id', 'idx_doc_snapshot_documento');

            $table->foreign('snapshot_revision_contractual_id', 'fk_doc_snapshot_snapshot')
                ->references('id')
                ->on('snapshots_revision_contractual')
                ->onDelete('cascade');

            $table->foreign('documento_revision_contractual_id', 'fk_doc_snapshot_documento')
                ->references('id')
                ->on('documentos_revision_contractual')
                ->onDelete('cascade');

            $table->foreign('user_id', 'fk_doc_snapshot_user')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('documento_snapshot_revision_contractual', function (Blueprint $table) {
            $table->dropForeign('fk_doc_snapshot_snapshot');
            $table->dropForeign('fk_doc_snapshot_documento');
            $table->dropForeign('fk_doc_snapshot_user');
        });

        Schema::dropIfExists('documento_snapshot_revision_contractual');
    }
};
