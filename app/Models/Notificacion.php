<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notificaciones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'notif_orden',
        'notif_fecha_puesta_dis_mat',
        'notif_numero_material',
        'notif_pedido_cliente',
        'notif_pos_pedido_cliente',
        'notif_cantidad_orden',
        'notif_cantidad_buena_notificada',
        'notif_referencia_colchon',
        'notif_nombre',
        'notif_denomin_posicion',
        'notif_estado_sistema',
        'notif_autor',
        'notif_fecha_creacion',
        'notif_hora_creacion',
        'notif_fecha_liberac_real',
        'notif_modificado_por',
        'notif_fecha_fin_notificada',
    ];
}