<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documentos_revision_contractual', function (Blueprint $table) {
            $table->unique(
                ['revision_contractual_id', 'hash_archivo'],
                'uq_doc_revision_contractual_hash'
            );
        });
    }

    public function down(): void
    {
        Schema::table('documentos_revision_contractual', function (Blueprint $table) {
            $table->dropUnique('uq_doc_revision_contractual_hash');
        });
    }
};