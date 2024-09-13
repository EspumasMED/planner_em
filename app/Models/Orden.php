<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        Log::debug("Iniciando cálculo con fechas: $startDate a $endDate");
        Log::debug("Incluir clientes: " . ($includeClientes ? 'Sí' : 'No'));
        Log::debug("Incluir stock: " . ($includeStock ? 'Sí' : 'No'));

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
                    'zona_pega', 'cierre', 'empaque',
                    'acolchadora_gribetz', 'acolchadora_china'
                ], 0),
                'totalClosures' => 0,
                'colchonesCantidad' => 0,
                'colchonetasCantidad' => 0,
                'metrosLinealesGribetz' => 0,
                'metrosLinealesChina' => 0,
                'cantidadColchonesCalibre1' => 0,
                'cantidadColchonesCalibre2' => 0,
                'cantidadColchonesCalibre3' => 0,
                'cantidadColchonesCalibre4' => 0,
                'totalColchonesChina' => 0,
                'totalColchonesGribetz' => 0,
                'distribucionCalibre2China' => 0,
                'distribucionCalibre2Gribetz' => 0,
                'porcentajeCalibre2China' => 0,
                'porcentajeCalibre2Gribetz' => 0,
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
            'zona_pega', 'cierre', 'empaque',
            'acolchadora_gribetz', 'acolchadora_china'
        ], 0);

        $totalClosures = 0;
        $colchonesCantidad = 0;
        $colchonetasCantidad = 0;
        $metrosLinealesGribetz = 0;
        $metrosLinealesChina = 0;
        $cantidadColchonesCalibre1 = 0;
        $cantidadColchonesCalibre2 = 0;
        $cantidadColchonesCalibre3 = 0;
        $cantidadColchonesCalibre4 = 0;
        $totalColchonesChina = 0;
        $totalColchonesGribetz = 0;

        Log::debug('Órdenes procesadas: ' . $orders->count());

        foreach ($orders as $order) {
            $referencia = $order->referencia_colchon;
            $quantity = (float) $order->total_quantity;

            Log::debug("Procesando orden: Referencia = $referencia, Cantidad = $quantity");

            if (!isset($timesByStation[$referencia])) {
                Log::warning("No se encontraron tiempos de producción para la referencia: $referencia");
                continue;
            }

            $times = $timesByStation[$referencia];

            foreach ($totalTimeByStation as $station => &$time) {
                if ($station != 'acolchadora_gribetz' && $station != 'acolchadora_china') {
                    $time = round($time + $quantity * (float) $times->$station, 1);
                }
            }

            $totalClosures = round($totalClosures + $quantity * $times->num_cierres, 1);

            $anchoTapa = self::getAnchoTapa($referencia); // En cm
            $anchoBanda = self::getAnchoBanda($referencia); // En cm
            $perimetro = self::getPerimetro($anchoTapa); // En cm
            
            Log::debug("Ancho de tapa: $anchoTapa cm, Ancho de banda: $anchoBanda cm, Perímetro: $perimetro cm");

            if ($anchoTapa == 0 || $anchoBanda == 0 || $perimetro == 0) {
                Log::warning("Datos inválidos para la referencia: $referencia");
                continue;
            }

            // Cálculo de metros lineales para bandas (todos los calibres)
            $bandasPorAncho = floor(200 / $anchoBanda); // Número de bandas que caben en el ancho de la máquina
            $metrosLinealesBandas = ($quantity * $perimetro) / $bandasPorAncho / 100; // Convertimos cm a m
            $metrosLinealesChina += $metrosLinealesBandas;

            Log::debug("Bandas por ancho: $bandasPorAncho, Metros lineales de bandas para China: $metrosLinealesBandas");

                Log::debug("Bandas por ancho: $bandasPorAncho, Metros lineales de bandas para China: $metrosLinealesBandas");

                // Cálculos para tapas
                $metrosLinealesTapas = ($anchoTapa * 2 * $quantity) / 100; // Convertimos cm a m

                switch ($times->calibre_colchon) {
                    case 1:
                        $cantidadColchonesCalibre1 += $quantity;
                        $metrosLinealesChina += $metrosLinealesTapas;
                        $totalColchonesChina += $quantity;
                        Log::debug("Metros lineales de tapas calibre 1 para China: $metrosLinealesTapas");
                        break;
                case 2:
                    $cantidadColchonesCalibre2 += $quantity;
                    // La distribución de calibre 2 se hará más adelante
                    Log::debug("Metros lineales de tapas calibre 2 acumulados: $metrosLinealesTapas");
                    break;
                case 3:
                case 4:
                    $cantidadColchonesCalibre3 += $quantity; // Combinamos calibre 3 y 4
                    $metrosLinealesGribetz += $metrosLinealesTapas;
                    $totalColchonesGribetz += $quantity;
                    Log::debug("Metros lineales de tapas calibre 3/4 para Gribetz: $metrosLinealesTapas");
                    break;
            }

            if (strpos(strtolower($referencia), 'col') === 0) {
                $colchonesCantidad += $quantity;
            } elseif (strpos(strtolower($referencia), 'cta') === 0) {
                $colchonetasCantidad += $quantity;
            }
        }

        // Distribuir tapas de calibre 2 entre Gribetz y China
        $totalTapasCalibr2 = ($cantidadColchonesCalibre2 * $anchoTapa * 2) / 100; // Convertimos cm a m
        $porcentajeGribetz = $metrosLinealesGribetz / ($metrosLinealesGribetz + $metrosLinealesChina);
        $porcentajeChina = 1 - $porcentajeGribetz;

        $tapasCalibr2Gribetz = $totalTapasCalibr2 * $porcentajeChina; // Cambiado de $porcentajeChina a $porcentajeGribetz
        $tapasCalibr2China = $totalTapasCalibr2 * $porcentajeGribetz; // Cambiado de $porcentajeGribetz a $porcentajeChina

        $metrosLinealesGribetz += $tapasCalibr2Gribetz;
        $metrosLinealesChina += $tapasCalibr2China;

        $distribucionCalibre2Gribetz = round($tapasCalibr2Gribetz / (($anchoTapa * 2) / 100)); // Convertimos m a cantidad
        $distribucionCalibre2China = $cantidadColchonesCalibre2 - $distribucionCalibre2Gribetz;

        $totalColchonesGribetz += $distribucionCalibre2Gribetz;
        $totalColchonesChina += $distribucionCalibre2China;

        // Asegurémonos de que los porcentajes estén correctamente asignados
        $porcentajeCalibre2Gribetz = $porcentajeGribetz * 100;
        $porcentajeCalibre2China = $porcentajeChina * 100;

        Log::debug("Distribución de tapas calibre 2: Gribetz: $tapasCalibr2Gribetz m ($porcentajeCalibre2Gribetz%), China: $tapasCalibr2China m ($porcentajeCalibre2China%)");

        // Calcular tiempo total para acolchadoras (50 metros por hora = 0.8333 metros por minuto)
        $metrosPorMinuto = 50 / 60;
        $tiempoTotalGribetz = round($metrosLinealesGribetz / $metrosPorMinuto, 1);
        $tiempoTotalChina = round($metrosLinealesChina / $metrosPorMinuto, 1);

        $totalTimeByStation['acolchadora_gribetz'] = $tiempoTotalGribetz;
        $totalTimeByStation['acolchadora_china'] = $tiempoTotalChina;

        Log::debug("Resumen final:");
        Log::debug("Total de órdenes procesadas: " . $orders->count());
        Log::debug("Metros lineales Gribetz: $metrosLinealesGribetz");
        Log::debug("Metros lineales China: $metrosLinealesChina");
        Log::debug("Tiempo total Gribetz: $tiempoTotalGribetz minutos");
        Log::debug("Tiempo total China: $tiempoTotalChina minutos");

        return [
            'totalTimeByStation' => $totalTimeByStation,
            'totalClosures' => $totalClosures,
            'colchonesCantidad' => $colchonesCantidad,
            'colchonetasCantidad' => $colchonetasCantidad,
            'metrosLinealesGribetz' => $metrosLinealesGribetz,
            'metrosLinealesChina' => $metrosLinealesChina,
            'cantidadColchonesCalibre1' => $cantidadColchonesCalibre1,
            'cantidadColchonesCalibre2' => $cantidadColchonesCalibre2,
            'cantidadColchonesCalibre3' => $cantidadColchonesCalibre3,
            'cantidadColchonesCalibre4' => 0, // Ahora está incluido en cantidadColchonesCalibre3
            'totalColchonesChina' => $totalColchonesChina,
            'totalColchonesGribetz' => $totalColchonesGribetz,
            'distribucionCalibre2China' => $distribucionCalibre2China,
            'distribucionCalibre2Gribetz' => $distribucionCalibre2Gribetz,
            'porcentajeCalibre2China' => $porcentajeCalibre2China,
            'porcentajeCalibre2Gribetz' => $porcentajeCalibre2Gribetz,
        ];
    }

    private static function getAnchoBanda($referencia)
    {
        if (preg_match('/\d+x\d+x(\d+)/i', $referencia, $matches)) {
            $anchoBanda = (int)$matches[1];
            Log::debug("getAnchoBanda: Referencia = $referencia, Ancho de banda encontrado = $anchoBanda cm");
            return $anchoBanda > 0 ? $anchoBanda : 0;
        }
        
        Log::warning("getAnchoBanda: No se pudo extraer el ancho de banda de la referencia: $referencia");
        return 0;
    }

    private static function getPerimetro($anchoTapa)
    {
        $perimetros = [
            100 => 590, // 5.9 metros en centímetros
            120 => 630, // 6.3 metros en centímetros
            140 => 670, // 6.7 metros en centímetros
            160 => 710, // 7.1 metros en centímetros
            200 => 810, // 8.1 metros en centímetros
        ];

        $perimetro = $perimetros[$anchoTapa] ?? 0;
        Log::debug("getPerimetro: Ancho de tapa = $anchoTapa cm, Perímetro calculado = $perimetro cm");
        if ($perimetro == 0) {
            Log::warning("No se encontró perímetro para el ancho de tapa: $anchoTapa cm");
        }
        return $perimetro;
    }

    private static function getAnchoTapa($referencia)
    {
        if (preg_match('/(\d+)x\d+x\d+/i', $referencia, $matches)) {
            $ancho = (int)$matches[1];
            Log::debug("getAnchoTapa: Referencia = $referencia, Ancho encontrado = $ancho cm");
            return $ancho > 0 ? $ancho : 0;
        }
        
        if (preg_match('/\b(\d{3})\b/', $referencia, $matches)) {
            $ancho = (int)$matches[1];
            Log::debug("getAnchoTapa: Referencia = $referencia, Ancho encontrado (alternativo) = $ancho cm");
            return $ancho > 0 ? $ancho : 0;
        }
        
        Log::warning("getAnchoTapa: No se pudo extraer el ancho de la referencia: $referencia");
        return 0;
    }
}