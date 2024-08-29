<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Orden extends Model
{
    use HasFactory;

    protected $table = 'ordenes';

    protected $fillable = [
        'orden', 'fecha_puesta_dis_mat', 'numero_material', 'pedido_cliente',
        'pos_pedido_cliente', 'cantidad_orden', 'cantidad_buena_notificada',
        'referencia_colchon', 'nombre', 'denomin_posicion', 'estado_sistema',
        'autor', 'fecha_creacion', 'hora_creacion', 'fecha_liberac_real',
        'modificado_por', 'fecha_fin_notificada',
    ];

    public static function calculateTotalProductionTimeByStation($startDate, $endDate, $includeClientes, $includeStock)
    {
        $query = self::whereBetween('fecha_creacion', [$startDate, $endDate]);

        if ($includeClientes && !$includeStock) {
            $query->where(function($q) {
                $q->whereNotNull('pedido_cliente')
                  ->where('pedido_cliente', '!=', '');
            });
        } elseif (!$includeClientes && $includeStock) {
            $query->where(function($q) {
                $q->whereNull('pedido_cliente')
                  ->orWhere('pedido_cliente', '');
            });
        } elseif (!$includeClientes && !$includeStock) {
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

        $orders = $query->select('referencia_colchon', DB::raw('SUM(cantidad_orden) as total_quantity'))
            ->groupBy('referencia_colchon')
            ->get();

        $timesByStation = TiempoProduccion::all()->keyBy('referencia_colchon');

        $totalTimeByStation = array_fill_keys([
            'fileteado_tapas', 'fileteado_falsos', 'maquina_rufflex', 
            'bordadora', 'decorado_falso', 'falso_pillow', 
            'encintado', 'maquina_plana', 'marquillado', 
            'zona_pega', 'cierre', 'empaque'
        ], 0);

        $totalClosures = 0;
        $colchonesCantidad = 0;
        $colchonetasCantidad = 0;

        foreach ($orders as $order) {
            $referencia = $order->referencia_colchon;
            $quantity = (float) $order->total_quantity;

            if (isset($timesByStation[$referencia])) {
                $times = $timesByStation[$referencia];

                foreach ($totalTimeByStation as $station => &$time) {
                    $time += $quantity * (float) $times->$station;
                }

                $totalClosures += $quantity * $times->num_cierres;
            }

            if (strpos(strtolower($referencia), 'col') === 0) {
                $colchonesCantidad += $quantity;
            } elseif (strpos(strtolower($referencia), 'cta') === 0) {
                $colchonetasCantidad += $quantity;
            }
        }

        return [
            'totalTimeByStation' => $totalTimeByStation,
            'totalClosures' => $totalClosures,
            'colchonesCantidad' => $colchonesCantidad,
            'colchonetasCantidad' => $colchonetasCantidad,
        ];
    }
}