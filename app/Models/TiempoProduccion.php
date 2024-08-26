<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiempoProduccion extends Model
{
    use HasFactory;

    // Especifica el nombre de la tabla en la base de datos
    protected $table = 'tiempos_produccion';

    // Define los campos que pueden ser asignados masivamente
    protected $fillable = [
        'referencia_colchon',
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
    ];

    /**
     * Obtiene los tiempos de producción para una referencia de colchón específica.
     *
     * @param string $referenciaColchon
     * @return array
     */
    public static function getTimesByReference($referenciaColchon)
    {
        // Busca los tiempos de producción para la referencia de colchón especificada en la tabla
        $times = self::where('referencia_colchon', $referenciaColchon)->first();

        // Si no se encuentra ningún registro, se devuelve un array con todos los tiempos en 0
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
            ];
        }

        // Si se encuentra el registro, se devuelve un array con los tiempos por estación
        // Utiliza el operador de coalescencia nula (??) para devolver 0 si algún valor está vacío o es nulo
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
        ];
    }
}
