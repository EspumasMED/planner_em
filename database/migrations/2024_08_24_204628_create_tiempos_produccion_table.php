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
        Schema::create('tiempos_produccion', function (Blueprint $table) {
            $table->id();
            $table->string('referencia_colchon'); // Referencia del colchón

            // Columnas para cada estación de trabajo, usando decimal para permitir tiempos con decimales
            $table->decimal('fileteado_tapas', 8, 2)->nullable(); // Tiempo en minutos en Fileteado de Tapas
            $table->decimal('fileteado_falsos', 8, 2)->nullable(); // Tiempo en Fileteado de Falsos
            $table->decimal('maquina_rufflex', 8, 2)->nullable(); // Tiempo en Máquina Rufflex
            $table->decimal('bordadora', 8, 2)->nullable(); // Tiempo en Bordadora
            $table->decimal('decorado_falso', 8, 2)->nullable(); // Tiempo en Decorado Falso
            $table->decimal('falso_pillow', 8, 2)->nullable(); // Tiempo en Falso Pillow
            $table->decimal('encintado', 8, 2)->nullable(); // Tiempo en Encintado
            $table->decimal('maquina_plana', 8, 2)->nullable(); // Tiempo en Máquina Plana
            $table->decimal('marquillado', 8, 2)->nullable(); // Tiempo en Marquillado
            $table->decimal('zona_pega', 8, 2)->nullable(); // Tiempo en Zona de Pega
            $table->decimal('cierre', 8, 2)->nullable(); // Tiempo en Cierre
            $table->decimal('empaque', 8, 2)->nullable(); // Tiempo en Empaque

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiempos_estaciones');
    }
};
