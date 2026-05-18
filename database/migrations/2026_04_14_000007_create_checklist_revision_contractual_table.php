<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_revision_contractual', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('snapshot_revision_contractual_id');
            $table->string('item', 255);
            $table->string('estado_item', 50);
            $table->longText('observacion')->nullable();
            $table->longText('referencia_documental')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->index('snapshot_revision_contractual_id', 'idx_checklist_snapshot');
            $table->index('estado_item', 'idx_checklist_estado');

            $table->foreign('snapshot_revision_contractual_id', 'fk_checklist_snapshot')
                ->references('id')
                ->on('snapshots_revision_contractual')
                ->onDelete('cascade');

            $table->foreign('user_id', 'fk_checklist_user')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('checklist_revision_contractual', function (Blueprint $table) {
            $table->dropForeign('fk_checklist_snapshot');
            $table->dropForeign('fk_checklist_user');
        });

        Schema::dropIfExists('checklist_revision_contractual');
    }
};