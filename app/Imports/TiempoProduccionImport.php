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
            TiempoProduccion::create([
                'referencia_colchon' => $row['referencia_colchon'],
                'num_cierres' => $row['num_cierres'],
                'fileteado_tapas' => $row['fileteado_tapas'],
                'fileteado_falsos' => $row['fileteado_falsos'],
                'maquina_rufflex' => $row['maquina_rufflex'],
                'bordadora' => $row['bordadora'],
                'decorado_falso' => $row['decorado_falso'],
                'falso_pillow' => $row['falso_pillow'],
                'encintado' => $row['encintado'],
                'maquina_plana' => $row['maquina_plana'],
                'marquillado' => $row['marquillado'],
                'zona_pega' => $row['zona_pega'],
                'cierre' => $row['cierre'],
                'empaque' => $row['empaque'],
            ]);
        }
    }
}