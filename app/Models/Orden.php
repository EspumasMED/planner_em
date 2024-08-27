<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Orden extends Model
{
    use HasFactory; // Usa el trait HasFactory para facilitar la creación de fábricas de modelos

    // Define el nombre de la tabla asociada a este modelo
    protected $table = 'ordenes';

    // Define los atributos que se pueden asignar masivamente
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

    // Método estático para calcular el tiempo total de producción por estación
    public static function calculateTotalProductionTimeByStation($startDate, $endDate)
    {
        // Obtiene las órdenes en el rango de fechas especificado
        $orders = DB::table('ordenes')
            ->whereBetween('fecha_creacion', [$startDate, $endDate])
            ->select('referencia_colchon', DB::raw('SUM(cantidad_orden) as total_quantity'))
            ->groupBy('referencia_colchon')
            ->get();

        // Obtiene los tiempos de producción por referencia de colchón
        $timesByStation = DB::table('tiempos_produccion')
            ->get()
            ->keyBy('referencia_colchon');

        // Inicializa un array con tiempos totales por estación
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

        // Recorre las órdenes para calcular el tiempo total por estación
        foreach ($orders as $order) {
            $referencia = $order->referencia_colchon; // Obtiene la referencia del colchón
            $quantity = (float) $order->total_quantity; // Obtiene la cantidad total de la orden

            if (isset($timesByStation[$referencia])) { // Verifica si hay tiempos para la referencia
                $times = $timesByStation[$referencia]; // Obtiene los tiempos para la referencia

                foreach ($totalTimeByStation as $station => &$time) { // Recorre cada estación
                    $time += $quantity * (float) $times->$station; // Calcula el tiempo total para cada estación
                }
            }
        }

        return $totalTimeByStation; // Retorna el tiempo total por estación
    }
}
