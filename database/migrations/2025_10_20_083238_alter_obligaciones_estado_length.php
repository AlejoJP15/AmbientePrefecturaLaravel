<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // SUBE de VARCHAR(10) a VARCHAR(50)
    public function up(): void
    {
        Schema::table('obligaciones', function (Blueprint $table) {
            // Aumenta la longitud a 50
            $table->string('estado', 50)->nullable()->change();
            // Quita "->nullable()" si tu columna NO permite nulos.
        });
    }

    // BAJA de VARCHAR(50) a VARCHAR(10) (ojo: podría truncar)
    public function down(): void
    {
        Schema::table('obligaciones', function (Blueprint $table) {
            // Si vuelves a 10, podrías necesitar truncar; con DBAL
            // a veces falla. En ese caso usa SQL crudo (ver Opción B).
            $table->string('estado', 10)->nullable()->change();
        });
    }
};
