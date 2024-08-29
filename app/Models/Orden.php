<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Orden extends Model
{
    // Incluye el trait HasFactory para generar instancias de este modelo usando factories
    use HasFactory;

    // Define el nombre de la tabla asociada a este modelo
    protected $table = 'ordenes';

    // Define los campos que se pueden asignar masivamente (mass assignment)
    protected $fillable = [
        'orden', 'fecha_puesta_dis_mat', 'numero_material', 'pedido_cliente',
        'pos_pedido_cliente', 'cantidad_orden', 'cantidad_buena_notificada',
        'referencia_colchon', 'nombre', 'denomin_posicion', 'estado_sistema',
        'autor', 'fecha_creacion', 'hora_creacion', 'fecha_liberac_real',
        'modificado_por', 'fecha_fin_notificada',
    ];

    /**
     * Este método calcula el tiempo total de producción por estación, el total de cierres,
     * y las cantidades de colchones y colchonetas producidos dentro de un rango de fechas.
     * Permite filtrar por pedidos de clientes o pedidos de stock.
     *
     * @param string $startDate Fecha de inicio para el filtro
     * @param string $endDate Fecha de fin para el filtro
     * @param bool $includeClientes Indica si se deben incluir solo pedidos de clientes
     * @param bool $includeStock Indica si se deben incluir solo pedidos de stock
     * @return array Un array con el tiempo total por estación, el total de cierres, y las cantidades de colchones y colchonetas
     */
    public static function calculateTotalProductionTimeByStation($startDate, $endDate, $includeClientes, $includeStock)
    {
        // Inicia una consulta filtrando por el rango de fechas de creación de las órdenes
        $query = self::whereBetween('fecha_creacion', [$startDate, $endDate]);

        // Aplica filtros adicionales según si se deben incluir pedidos de clientes o de stock
        if ($includeClientes && !$includeStock) {
            $query->whereNotNull('pedido_cliente'); // Solo pedidos de clientes
        } elseif (!$includeClientes && $includeStock) {
            $query->whereNull('pedido_cliente'); // Solo pedidos de stock
        } elseif (!$includeClientes && !$includeStock) {
            // Si no se incluyen ni pedidos de clientes ni de stock, retorna un array de resultados vacíos
            return [
                'totalTimeByStation' => array_fill_keys([
                    'fileteado_tapas', 'fileteado_falsos', 'maquina_rufflex', 
                    'bordadora', 'decorado_falso', 'falso_pillow', 
                    'encintado', 'maquina_plana', 'marquillado', 
                    'zona_pega', 'cierre', 'empaque'
                ], 0),
                'totalClosures' => 0,
                'colchonesCantidad' => 0,
                'colchonetasCantidad' => 0,
            ];
        }

        // Obtiene las órdenes agrupadas por referencia de colchón y calcula la cantidad total por referencia
        $orders = $query->select('referencia_colchon', DB::raw('SUM(cantidad_orden) as total_quantity'))
            ->groupBy('referencia_colchon')
            ->get();

        // Obtiene los tiempos de producción por referencia de colchón desde el modelo TiempoProduccion
        $timesByStation = TiempoProduccion::all()->keyBy('referencia_colchon');

        // Inicializa un array para almacenar el tiempo total por estación, inicialmente en 0
        $totalTimeByStation = array_fill_keys([
            'fileteado_tapas', 'fileteado_falsos', 'maquina_rufflex', 
            'bordadora', 'decorado_falso', 'falso_pillow', 
            'encintado', 'maquina_plana', 'marquillado', 
            'zona_pega', 'cierre', 'empaque'
        ], 0);

        // Inicializa variables para el total de cierres y las cantidades de colchones y colchonetas
        $totalClosures = 0;
        $colchonesCantidad = 0;
        $colchonetasCantidad = 0;

        // Itera sobre las órdenes para calcular los tiempos de producción y las cantidades
        foreach ($orders as $order) {
            $referencia = $order->referencia_colchon;
            $quantity = (float) $order->total_quantity;

            // Si se encuentran tiempos de producción para la referencia actual
            if (isset($timesByStation[$referencia])) {
                $times = $timesByStation[$referencia];

                // Suma el tiempo total por estación para cada estación en particular
                foreach ($totalTimeByStation as $station => &$time) {
                    $time += $quantity * (float) $times->$station;
                }

                // Suma el total de cierres multiplicado por la cantidad de la orden
                $totalClosures += $quantity * $times->num_cierres;
            }

            // Identifica si la referencia es de colchones o colchonetas y suma la cantidad correspondiente
            if (strpos(strtolower($referencia), 'col') === 0) {
                $colchonesCantidad += $quantity;
            } elseif (strpos(strtolower($referencia), 'cta') === 0) {
                $colchonetasCantidad += $quantity;
            }
        }

        // Retorna el resultado con el tiempo total por estación, el total de cierres, y las cantidades producidas
        return [
            'totalTimeByStation' => $totalTimeByStation,
            'totalClosures' => $totalClosures,
            'colchonesCantidad' => $colchonesCantidad,
            'colchonetasCantidad' => $colchonetasCantidad,
        ];
    }
}
