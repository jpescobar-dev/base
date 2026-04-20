<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checklist_revision_contractual', function (Blueprint $table) {
            if (!Schema::hasColumn('checklist_revision_contractual', 'tipo_checklist')) {
                $table->string('tipo_checklist', 30)->nullable()->after('estado_item');
                $table->index('tipo_checklist', 'idx_checklist_tipo_checklist');
            }
        });
    }

    public function down(): void
    {
        Schema::table('checklist_revision_contractual', function (Blueprint $table) {
            if (Schema::hasColumn('checklist_revision_contractual', 'tipo_checklist')) {
                $table->dropIndex('idx_checklist_tipo_checklist');
                $table->dropColumn('tipo_checklist');
            }
        });
    }
};
