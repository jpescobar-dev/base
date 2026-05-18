<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revisiones_contractuales', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 255);
            $table->text('descripcion')->nullable();
            $table->foreignId('estado_id')->constrained('estados');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();

            $table->index('estado_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisiones_contractuales');
    }
};