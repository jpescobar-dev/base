<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hallazgos_revision_contractual', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('snapshot_revision_contractual_id');
            $table->unsignedBigInteger('estado_id')->nullable();
            $table->unsignedBigInteger('user_id');

            $table->string('titulo', 255);
            $table->string('tipo_hallazgo', 50)->nullable();
            $table->string('tipo_riesgo', 100)->nullable();
            $table->string('nivel_criticidad', 50)->nullable();

            $table->longText('hecho_acreditado')->nullable();
            $table->longText('observacion')->nullable();
            $table->longText('fundamento_documental')->nullable();
            $table->longText('consecuencia_posible')->nullable();
            $table->longText('recomendacion')->nullable();

            $table->timestamps();

            $table->index('snapshot_revision_contractual_id', 'idx_hallazgo_snapshot');
            $table->index('estado_id', 'idx_hallazgo_estado');
            $table->index('tipo_hallazgo', 'idx_hallazgo_tipo');
            $table->index('nivel_criticidad', 'idx_hallazgo_criticidad');

            $table->foreign('snapshot_revision_contractual_id', 'fk_hallazgo_snapshot')
                ->references('id')
                ->on('snapshots_revision_contractual')
                ->onDelete('cascade');

            $table->foreign('estado_id', 'fk_hallazgo_estado')
                ->references('id')
                ->on('estados')
                ->nullOnDelete();

            $table->foreign('user_id', 'fk_hallazgo_user')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('hallazgos_revision_contractual', function (Blueprint $table) {
            $table->dropForeign('fk_hallazgo_snapshot');
            $table->dropForeign('fk_hallazgo_estado');
            $table->dropForeign('fk_hallazgo_user');
        });

        Schema::dropIfExists('hallazgos_revision_contractual');
    }
};