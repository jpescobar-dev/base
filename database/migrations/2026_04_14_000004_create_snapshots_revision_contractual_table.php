<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('snapshots_revision_contractual', function (Blueprint $table) {
            $table->id();

            $table->foreignId('revision_contractual_id')
                ->constrained('revisiones_contractuales')
                ->cascadeOnDelete();

            $table->unsignedInteger('numero_version');
            $table->string('tipo_ejecucion', 50)->default('manual');
            $table->longText('resumen')->nullable();
            $table->json('json_resultado')->nullable();
            $table->boolean('es_actual')->default(false);

            $table->foreignId('user_id')->constrained('users');

            $table->timestamps();

            $table->unique(
                ['revision_contractual_id', 'numero_version'],
                'uq_revision_contractual_numero_version'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('snapshots_revision_contractual');
    }
};