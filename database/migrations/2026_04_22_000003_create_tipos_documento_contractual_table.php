<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_documento_contractual', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 80)->unique();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->string('jerarquia', 20)->default('media');
            $table->unsignedInteger('peso_jerarquico')->default(50);
            $table->boolean('es_critico')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('jerarquia');
            $table->index('es_critico');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_documento_contractual');
    }
};
