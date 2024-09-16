<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mantenimientos_programados', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_mantenimiento');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('estacion_trabajo');
            $table->integer('numero_maquinas');
            $table->text('descripcion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientos_programados');
    }
};