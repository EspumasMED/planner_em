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
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();
            $table->string('orden')->nullable();
            $table->date('fecha_puesta')->nullable();
            $table->string('numero_material')->nullable();
            $table->string('pedido_cliente')->nullable();
            $table->string('pos_pedido')->nullable();
            $table->integer('cantidad_orden')->nullable();
            $table->integer('notificados')->nullable();
            $table->string('referencia_colchon')->nullable();
            $table->string('nombre_cliente')->nullable();
            $table->string('denomin_posicion')->nullable();
            $table->string('estado_sistema')->nullable();
            $table->string('autor')->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->timestamp('hora_creacion')->useCurrent(); // Usa timestamp para hora actual
            $table->date('fecha_liberacion')->nullable();
            $table->string('modificado')->nullable();
            $table->date('fecha_fin_notificada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};
