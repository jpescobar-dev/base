<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revision_tipos_documento', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('revision_contractual_id');
            $table->unsignedBigInteger('tipo_documento_contractual_id');
            $table->boolean('aplica')->default(true);
            $table->boolean('obligatorio')->default(false);
            $table->text('observacion')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->unique(
                ['revision_contractual_id', 'tipo_documento_contractual_id'],
                'uq_revision_tipo_documento'
            );

            $table->foreign('revision_contractual_id', 'fk_rev_tipo_revision')
                ->references('id')
                ->on('revisiones_contractuales')
                ->onDelete('cascade');

            $table->foreign('tipo_documento_contractual_id', 'fk_rev_tipo_tipo')
                ->references('id')
                ->on('tipos_documento_contractual')
                ->onDelete('cascade');

            $table->foreign('user_id', 'fk_rev_tipo_user')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('revision_tipos_documento', function (Blueprint $table) {
            $table->dropForeign('fk_rev_tipo_revision');
            $table->dropForeign('fk_rev_tipo_tipo');
            $table->dropForeign('fk_rev_tipo_user');
        });

        Schema::dropIfExists('revision_tipos_documento');
    }
};
