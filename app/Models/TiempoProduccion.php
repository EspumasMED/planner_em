<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiempoProduccion extends Model
{
    use HasFactory;

    protected $table = 'tiempos_produccion';

    protected $fillable = [
        'referencia_colchon',
        'num_cierres',
        'fileteado_tapas',
        'fileteado_falsos',
        'maquina_rufflex',
        'bordadora',
        'decorado_falso',
        'falso_pillow',
        'encintado',
        'maquina_plana',
        'marquillado',
        'zona_pega',
        'cierre',
        'empaque',
        'acolchadora_gribetz',
        'acolchadora_china',
        'ancho_banda',
        'calibre_colchon',
    ];

    /**
     * Obtiene los tiempos de producción para una referencia de colchón específica.
     *
     * @param string $referenciaColchon
     * @return array
     */
    public static function getTimesByReference($referenciaColchon)
    {
        $times = self::where('referencia_colchon', $referenciaColchon)->first();

        if (!$times) {
            return [
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
                'acolchadora_gribetz' => 0.83, // Metros lineales por minuto
                'acolchadora_china' => 0.83,   // Metros lineales por minuto
                'ancho_banda' => 0,
                'calibre_colchon' => 0,
                'num_cierres' => 0,
            ];
        }

        return [
            'fileteado_tapas' => $times->fileteado_tapas ?? 0,
            'fileteado_falsos' => $times->fileteado_falsos ?? 0,
            'maquina_rufflex' => $times->maquina_rufflex ?? 0,
            'bordadora' => $times->bordadora ?? 0,
            'decorado_falso' => $times->decorado_falso ?? 0,
            'falso_pillow' => $times->falso_pillow ?? 0,
            'encintado' => $times->encintado ?? 0,
            'maquina_plana' => $times->maquina_plana ?? 0,
            'marquillado' => $times->marquillado ?? 0,
            'zona_pega' => $times->zona_pega ?? 0,
            'cierre' => $times->cierre ?? 0,
            'empaque' => $times->empaque ?? 0,
            'acolchadora_gribetz' => $times->acolchadora_gribetz ?? 0.83,
            'acolchadora_china' => $times->acolchadora_china ?? 0.83,
            'ancho_banda' => $times->ancho_banda ?? 0,
            'calibre_colchon' => $times->calibre_colchon ?? 0,
            'num_cierres' => $times->num_cierres ?? 0,
        ];
    }

    /**
     * Obtiene el ancho de banda para una referencia de colchón específica.
     *
     * @param string $referenciaColchon
     * @return float
     */
    public static function getAnchoBanda($referenciaColchon)
    {
        $tiempo = self::where('referencia_colchon', $referenciaColchon)->first();
        return $tiempo ? $tiempo->ancho_banda : 0;
    }

    /**
     * Obtiene el calibre del colchón para una referencia específica.
     *
     * @param string $referenciaColchon
     * @return int
     */
    public static function getCalibreColchon($referenciaColchon)
    {
        $tiempo = self::where('referencia_colchon', $referenciaColchon)->first();
        return $tiempo ? $tiempo->calibre_colchon : 0;
    }
}