<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     * Crea la tabla 'capacidades' con todos los campos necesarios.
     */
    public function up(): void
    {
        Schema::create('capacidades', function (Blueprint $table) {
            $table->id();
            // Campo para almacenar el nombre de la estación de trabajo
            $table->string('estacion_trabajo');
            // Campo para almacenar el número de máquinas
            $table->integer('numero_maquinas');
            // Campo para almacenar el tiempo de jornada ajustable
            $table->integer('tiempo_jornada');
            // Campo para almacenar el tiempo de jornada original (100%)
            $table->integer('tiempo_jornada_original')->nullable();
            // Campos para almacenar las marcas de tiempo de creación y actualización
            $table->timestamps();
        });

        // Aseguramos que 'tiempo_jornada_original' tenga el mismo valor que 'tiempo_jornada' inicialmente
        DB::statement('UPDATE capacidades SET tiempo_jornada_original = tiempo_jornada');
    }

    /**
     * Revierte las migraciones.
     * Elimina la tabla 'capacidades'.
     */
    public function down(): void
    {
        Schema::dropIfExists('capacidades');
    }
};