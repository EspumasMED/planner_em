<?php

namespace App\Models; // El modelo pertenece al namespace App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory; // Importa el trait HasFactory para usar fábricas en Eloquent
use Illuminate\Database\Eloquent\Model; // Importa la clase Model de Eloquent
use Illuminate\Support\Facades\DB; // Importa la clase DB para realizar consultas directas a la base de datos

class Orden extends Model // Define la clase Orden que extiende de Model, lo que significa que es un modelo de Eloquent
{
    use HasFactory; // Habilita el uso de fábricas para este modelo

    protected $table = 'ordenes'; // Especifica el nombre de la tabla en la base de datos con la que se relaciona este modelo

    // Define los campos que se pueden asignar en masa (mass assignment)
    protected $fillable = [
        'orden', // Número o identificador de la orden
        'fecha_puesta_dis_mat', // Fecha en que se dispuso el material
        'numero_material', // Número de identificación del material
        'pedido_cliente', // Identificación del pedido del cliente (puede ser nulo si es para stock)
        'pos_pedido_cliente', // Posición del pedido del cliente
        'cantidad_orden', // Cantidad de producto en la orden
        'cantidad_buena_notificada', // Cantidad de productos buenos notificados
        'referencia_colchon', // Referencia del colchón
        'nombre', // Nombre del producto o colchón
        'denomin_posicion', // Denominación de la posición del pedido
        'estado_sistema', // Estado del sistema para la orden (ej. en proceso, completada)
        'autor', // Autor que creó o modificó la orden
        'fecha_creacion', // Fecha de creación de la orden
        'hora_creacion', // Hora de creación de la orden
        'fecha_liberac_real', // Fecha de liberación real de la orden
        'modificado_por', // Usuario que modificó la orden
        'fecha_fin_notificada', // Fecha en la que se notificó el fin de la orden
    ];

    // Método estático para calcular el tiempo total de producción por estación de trabajo
    public static function calculateTotalProductionTimeByStation($startDate, $endDate, $includeClientes, $includeStock)
    {
        // Consulta base: Filtra las órdenes dentro del rango de fechas especificado
        $query = self::whereBetween('fecha_creacion', [$startDate, $endDate]);

        // Filtra las órdenes según si se incluyen pedidos de clientes o de stock
        if ($includeClientes && !$includeStock) {
            $query->whereNotNull('pedido_cliente'); // Incluir solo pedidos de clientes
        } elseif (!$includeClientes && $includeStock) {
            $query->whereNull('pedido_cliente'); // Incluir solo pedidos de stock
        } elseif (!$includeClientes && !$includeStock) {
            // Si no se incluyen ni clientes ni stock, devuelve un conjunto de tiempos vacíos para todas las estaciones
            return [
                'totalTimeByStation' => array_fill_keys([
                    'fileteado_tapas', 'fileteado_falsos', 'maquina_rufflex', 
                    'bordadora', 'decorado_falso', 'falso_pillow', 
                    'encintado', 'maquina_plana', 'marquillado', 
                    'zona_pega', 'cierre', 'empaque'
                ], 0),
                'totalClosures' => 0,
            ];
        }

        // Agrupa las órdenes por referencia del colchón y calcula la cantidad total por referencia
        $orders = $query->select('referencia_colchon', DB::raw('SUM(cantidad_orden) as total_quantity'))
            ->groupBy('referencia_colchon')
            ->get();

        // Obtiene los tiempos de producción por referencia de colchón y los organiza por referencia
        $timesByStation = TiempoProduccion::all()->keyBy('referencia_colchon');

        // Inicializa un array con los nombres de las estaciones de trabajo y sus tiempos totales en 0
        $totalTimeByStation = array_fill_keys([
            'fileteado_tapas', 'fileteado_falsos', 'maquina_rufflex', 
            'bordadora', 'decorado_falso', 'falso_pillow', 
            'encintado', 'maquina_plana', 'marquillado', 
            'zona_pega', 'cierre', 'empaque'
        ], 0);

        $totalClosures = 0; // Inicializa el total de cierres en 0

        // Recorre cada orden obtenida
        foreach ($orders as $order) {
            $referencia = $order->referencia_colchon; // Referencia del colchón
            $quantity = (float) $order->total_quantity; // Cantidad total de la orden

            // Si existen tiempos de producción para esa referencia de colchón
            if (isset($timesByStation[$referencia])) {
                $times = $timesByStation[$referencia]; // Obtiene los tiempos para esa referencia

                // Recorre cada estación de trabajo y acumula los tiempos multiplicados por la cantidad
                foreach ($totalTimeByStation as $station => &$time) {
                    $time += $quantity * (float) $times->$station; // Incrementa el tiempo por estación
                }

                // Incrementa el total de cierres
                $totalClosures += $quantity * $times->num_cierres;
            }
        }

        // Retorna el tiempo total por estación de trabajo y el total de cierres
        return [
            'totalTimeByStation' => $totalTimeByStation,
            'totalClosures' => $totalClosures,
        ];
    }
}
