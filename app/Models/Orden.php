<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Orden extends Model
{
    use HasFactory;
    
    // Especifica el nombre de la tabla en la base de datos
    protected $table = 'ordenes';

    // Define los campos que pueden ser asignados masivamente
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

    /**
     * Calcula el tiempo total de producción por estación para todas las órdenes.
     *
     * @return array Tiempo total de producción por estación.
     */
    public static function calculateTotalProductionTimeByStation()
    {
        // Obtener las órdenes agrupadas por 'referencia_colchon' y sumar la cantidad total de cada referencia
        $orders = DB::table('ordenes')
            ->select('referencia_colchon', DB::raw('SUM(cantidad_orden) as total_quantity'))
            ->groupBy('referencia_colchon')
            ->get();

        // Obtener los tiempos de producción por estación desde la tabla 'tiempos_produccion'
        // keyBy() organiza los resultados por 'referencia_colchon', permitiendo acceso directo a los tiempos por referencia
        $timesByStation = DB::table('tiempos_produccion')
            ->get()
            ->keyBy('referencia_colchon');

        // Inicializar un array para almacenar el tiempo total por estación
        $totalTimeByStation = [
            'fileteado_tapas' => 0,
            'fileteado_falsos' => 0,
            'maquina_rufflex' => 0,
            'bordadora' => 0,
            'decorado_falso' => 0,
            'falso_pillow' => 0,
            'encintado' => 0,
            'maquina_plana' => 0,
            'marquillado' => 0,
            'zona_pega' => 0,
            'cierre' => 0,
            'empaque' => 0,
        ];

        // Recorrer cada orden para calcular el tiempo total por estación
        foreach ($orders as $order) {
            // Obtener la referencia del colchón y la cantidad total de órdenes para esa referencia
            $referencia = $order->referencia_colchon;
            $quantity = (float) $order->total_quantity; // Convertir la cantidad a número flotante

            // Verificar si hay tiempos de producción asociados a esta referencia
            if (isset($timesByStation[$referencia])) {
                // Obtener los tiempos de producción para la referencia actual
                $times = $timesByStation[$referencia];

                // Multiplicar la cantidad de órdenes por el tiempo de producción correspondiente y sumarlo al tiempo total por estación
                $totalTimeByStation['fileteado_tapas'] += $quantity * (float) $times->fileteado_tapas;
                $totalTimeByStation['fileteado_falsos'] += $quantity * (float) $times->fileteado_falsos;
                $totalTimeByStation['maquina_rufflex'] += $quantity * (float) $times->maquina_rufflex;
                $totalTimeByStation['bordadora'] += $quantity * (float) $times->bordadora;
                $totalTimeByStation['decorado_falso'] += $quantity * (float) $times->decorado_falso;
                $totalTimeByStation['falso_pillow'] += $quantity * (float) $times->falso_pillow;
                $totalTimeByStation['encintado'] += $quantity * (float) $times->encintado;
                $totalTimeByStation['maquina_plana'] += $quantity * (float) $times->maquina_plana;
                $totalTimeByStation['marquillado'] += $quantity * (float) $times->marquillado;
                $totalTimeByStation['zona_pega'] += $quantity * (float) $times->zona_pega;
                $totalTimeByStation['cierre'] += $quantity * (float) $times->cierre;
                $totalTimeByStation['empaque'] += $quantity * (float) $times->empaque;
            }
        }

        // Devolver el tiempo total de producción calculado por estación
        return $totalTimeByStation;
    }
}
