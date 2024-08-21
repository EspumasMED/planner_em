<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    protected $table = 'ordenes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'orden',
        'fecha_puesta_dis_mat',
        'numero_material',
        'pedido_cliente',
        'pos_pedido_cliente',
        'cantidad_orden',
        'cantidad_buena_notificada',
        'referencia_colchon',
        'nombre',
        'denomin_posicion',
        'estado_sistema',
        'autor',
        'fecha_creacion',
        'hora_creacion',
        'fecha_liberac_real',
        'modificado_por',
        'fecha_fin_notificada',
    ];
    
}
