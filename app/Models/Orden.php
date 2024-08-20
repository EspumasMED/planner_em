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
        'fecha_puesta',
        'numero_material',
        'pedido_cliente',
        'pos_pedido',
        'cantidad_orden',
        'notificados',
        'referencia_colchon',
        'nombre_cliente',
        'denomin_posicion',
        'estado_sistema',
        'autor',
        'fecha_creacion',
        'hora_creacion', // Asegúrate de incluir este campo
        'fecha_liberacion',
        'modificado',
        'fecha_fin_notificada',
    ];
    
    protected $casts = [
        'hora_creacion' => 'datetime',
    ];
}
