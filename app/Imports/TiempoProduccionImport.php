<?php

namespace App\Imports;

use App\Models\TiempoProduccion;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class TiempoProduccionImport implements WithHeadingRow, ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $data = [];

            $fields = [
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
                'calibre_colchon'
            ];

            foreach ($fields as $field) {
                if (isset($row[$field]) && $row[$field] !== '') {
                    $data[$field] = $row[$field];
                }
            }

            if (!empty($data)) {
                TiempoProduccion::create($data);
            }
        }
    }
}