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
            Log::info('Processing row:', $row->toArray()); // Agrega un log para ver el contenido de cada fila

            // Verificar que el campo 'orden' no esté vacío
            if (empty($row['orden'])) {
                Log::info('Skipping row due to empty orden:', $row->toArray());
                continue; // Omitir este registro
            }

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

            Log::info('Data to insert:', $data);

            // Añadir al array de inserción solo si los datos son válidos
            if ($this->isValidData($data)) {
                $dataToInsert[] = $data;
            }
        }

        // Inserta todos los registros válidos al final del loop
        if (!empty($dataToInsert)) {
            Log::info('Inserting data:', $dataToInsert); // Agrega un log para ver los datos a insertar
            Orden::insert($dataToInsert); // Inserción masiva
        } else {
            Log::info('No valid data to insert.');
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

    private function isValidData($data)
    {
        // Implementa validaciones adicionales si es necesario
        return !empty($data['orden']);
    }
}
