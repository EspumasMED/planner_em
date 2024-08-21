<?php

namespace App\Imports;

use App\Models\Orden;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OrdenImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $dataToInsert = []; // Array para almacenar todos los registros

        foreach ($rows as $row) {
            try {
                $data = [
                    'orden' => $row['orden'] ?? null,
                    'fecha_puesta_dis_mat' => $this->parseExcelDate($row['fecha_puesta_dismat']),
                    'numero_material' => $row['numero_material'] ?? null,
                    'pedido_cliente' => $row['pedido_cliente'] ?? null,
                    'pos_pedido_cliente' => $row['pospedido_cliente'] ?? null,
                    'cantidad_orden' => isset($row['cantidad_orden_gmein']) ? (int) $row['cantidad_orden_gmein'] : 0,
                    'cantidad_buena_notificada' => isset($row['cantidad_buena_notificada_gmein']) ? (int) $row['cantidad_buena_notificada_gmein'] : 0,
                    'referencia_colchon' => $row['texto_breve_material'] ?? null,
                    'nombre' => $row['nombre'] ?? null,
                    'denomin_posicion' => $row['denominposicion'] ?? null,
                    'estado_sistema' => $row['estado_de_sistema'] ?? null,
                    'autor' => $row['autor'] ?? null,
                    'fecha_creacion' => $this->parseExcelDate($row['fecha_de_creacion']),
                    'hora_creacion' => $this->parseExcelTime($row['hora_creacion']),
                    'fecha_liberac_real' => $this->parseExcelDate($row['fecha_liberacreal']),
                    'modificado_por' => $row['modificado_por'] ?? null,
                    'fecha_fin_notificada' => $this->parseExcelDate($row['fecha_fin_notificada']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // // Verificar que las fechas y horas sean válidas
                // if (!$data['fecha_puesta_dis_mat'] || !$data['fecha_creacion'] || !$data['fecha_liberac_real'] || !$data['fecha_fin_notificada'] || !$data['hora_creacion']) {
                //     Log::warning('Registro omitido por fechas inválidas', $data);
                //     continue;
                // }

                // Añadir al array de inserción
                $dataToInsert[] = $data;

            } catch (\Exception $e) {
                Log::error('Error al preparar registro para la inserción: ' . $e->getMessage(), $data);
                continue;
            }
        }

        // Inserta todos los registros al final del loop
        if (!empty($dataToInsert)) {
            Orden::insert($dataToInsert); // Inserción masiva
        }
    }

    private function parseExcelDate($excelDate)
    {
        if (!$excelDate) {
            return null;
        }

        $baseDate = Carbon::createFromFormat('Y-m-d', '1899-12-30');
        try {
            return $baseDate->addDays($excelDate)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseExcelTime($excelTime)
    {
        if (!$excelTime) {
            return null;
        }

        try {
            $hours = floor($excelTime * 24);
            $minutes = floor(($excelTime * 24 - $hours) * 60);
            $seconds = floor((($excelTime * 24 - $hours) * 60 - $minutes) * 60);
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        } catch (\Exception $e) {
            return null;
        }
    }
}
