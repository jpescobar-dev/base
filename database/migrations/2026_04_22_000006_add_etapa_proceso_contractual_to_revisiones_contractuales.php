<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('revisiones_contractuales', function (Blueprint $table) {
            $table->string('etapa_proceso_contractual', 40)
                ->default('pre_adjudicacion')
                ->after('descripcion');

            $table->index('etapa_proceso_contractual', 'idx_revision_etapa_proceso');
        });
    }

    public function down(): void
    {
        Schema::table('revisiones_contractuales', function (Blueprint $table) {
            $table->dropIndex('idx_revision_etapa_proceso');
            $table->dropColumn('etapa_proceso_contractual');
        });
    }
};
