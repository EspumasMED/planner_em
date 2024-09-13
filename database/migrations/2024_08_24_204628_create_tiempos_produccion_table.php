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
            $table->string('referencia_colchon');
            $table->integer('num_cierres');

            // Columnas para cada estación de trabajo existente
            $table->decimal('fileteado_tapas', 8, 2)->nullable();
            $table->decimal('fileteado_falsos', 8, 2)->nullable();
            $table->decimal('maquina_rufflex', 8, 2)->nullable();
            $table->decimal('bordadora', 8, 2)->nullable();
            $table->decimal('decorado_falso', 8, 2)->nullable();
            $table->decimal('falso_pillow', 8, 2)->nullable();
            $table->decimal('encintado', 8, 2)->nullable();
            $table->decimal('maquina_plana', 8, 2)->nullable();
            $table->decimal('marquillado', 8, 2)->nullable();
            $table->decimal('zona_pega', 8, 2)->nullable();
            $table->decimal('cierre', 8, 2)->nullable();
            $table->decimal('empaque', 8, 2)->nullable();

            // Nuevas columnas para las acolchadoras (metros lineales por minuto)
            $table->decimal('acolchadora_gribetz', 8, 2)->nullable()->default(0.83);
            $table->decimal('acolchadora_china', 8, 2)->nullable()->default(0.83);

            // Columnas adicionales para cálculos de metros lineales
            $table->decimal('ancho_banda', 8, 2)->nullable();
            $table->integer('calibre_colchon')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiempos_produccion');
    }
};