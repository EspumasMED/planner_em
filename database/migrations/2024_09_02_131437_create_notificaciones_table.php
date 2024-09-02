<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->string('notif_orden')->nullable(); // Orden
            $table->date('notif_fecha_puesta_dis_mat')->nullable(); // Fecha puesta dis.Mat
            $table->string('notif_numero_material')->nullable(); // Número material
            $table->string('notif_pedido_cliente')->nullable(); // Pedido cliente
            $table->string('notif_pos_pedido_cliente')->nullable(); // Pos. pedido cliente
            $table->integer('notif_cantidad_orden')->nullable(); // Cantidad orden (GMEIN)
            $table->integer('notif_cantidad_buena_notificada')->nullable(); // Cantidad buena notificada (GMEIN)
            $table->string('notif_referencia_colchon')->nullable(); // Texto breve material
            $table->string('notif_nombre')->nullable(); // Nombre
            $table->string('notif_denomin_posicion')->nullable(); // Denomin. posición
            $table->string('notif_estado_sistema')->nullable(); // Estado de sistema
            $table->string('notif_autor')->nullable(); // Autor
            $table->date('notif_fecha_creacion')->nullable(); // Fecha de creación
            $table->time('notif_hora_creacion')->nullable(); // Hora creación
            $table->date('notif_fecha_liberac_real')->nullable(); // Fecha liberac. real
            $table->string('notif_modificado_por')->nullable(); // Modificado por
            $table->date('notif_fecha_fin_notificada')->nullable(); // Fecha fin notificada
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
        Schema::dropIfExists('notificaciones');
    }
}