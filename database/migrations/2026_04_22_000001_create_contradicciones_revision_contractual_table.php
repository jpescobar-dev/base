<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contradicciones_revision_contractual', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('snapshot_revision_contractual_id');
            $table->string('campo', 100)->nullable();
            $table->string('etiqueta', 150);
            $table->string('criticidad', 30)->nullable();
            $table->longText('descripcion')->nullable();
            $table->json('valores_detectados')->nullable();
            $table->longText('recomendacion')->nullable();
            $table->string('documento_preferente', 255)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->index('snapshot_revision_contractual_id', 'idx_contradiccion_snapshot');
            $table->index('campo', 'idx_contradiccion_campo');
            $table->index('criticidad', 'idx_contradiccion_criticidad');

            $table->foreign('snapshot_revision_contractual_id', 'fk_contradiccion_snapshot')
                ->references('id')
                ->on('snapshots_revision_contractual')
                ->onDelete('cascade');

            $table->foreign('user_id', 'fk_contradiccion_user')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('contradicciones_revision_contractual', function (Blueprint $table) {
            $table->dropForeign('fk_contradiccion_snapshot');
            $table->dropForeign('fk_contradiccion_user');
        });

        Schema::dropIfExists('contradicciones_revision_contractual');
    }
};
