<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();
            $table->string('orden')->nullable(); // Orden
            $table->date('fecha_puesta_dis_mat')->nullable(); // Fecha puesta dis.Mat
            $table->string('numero_material')->nullable(); // Número material
            $table->string('pedido_cliente')->nullable(); // Pedido cliente
            $table->string('pos_pedido_cliente')->nullable(); // Pos. pedido cliente
            $table->integer('cantidad_orden')->nullable(); // Cantidad orden (GMEIN)
            $table->integer('cantidad_buena_notificada')->nullable(); // Cantidad buena notificada (GMEIN)
            $table->string('Referencia_colchon')->nullable(); // Texto breve material
            $table->string('nombre')->nullable(); // Nombre
            $table->string('denomin_posicion')->nullable(); // Denomin. posición
            $table->string('estado_sistema')->nullable(); // Estado de sistema
            $table->string('autor')->nullable(); // Autor
            $table->date('fecha_creacion')->nullable(); // Fecha de creación
            $table->time('hora_creacion')->nullable(); // Hora creación
            $table->date('fecha_liberac_real')->nullable(); // Fecha liberac. real
            $table->string('modificado_por')->nullable(); // Modificado por
            $table->date('fecha_fin_notificada')->nullable(); // Fecha fin notificada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordenes');
    }
}
