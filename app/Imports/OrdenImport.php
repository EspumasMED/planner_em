<?php

namespace App\Imports;

use App\Models\Orden;
use App\Models\TiempoProduccion;
use App\Models\ColchonSinTiempo;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrdenImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            $dataToInsert = []; // Array para almacenar todos los registros

            foreach ($rows as $row) {
                Log::info('Processing row:', $row->toArray());

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

                // Verificar si la referencia del colchón existe en TiempoProduccion
                if (!empty($data['referencia_colchon'])) {
                    $existeTiempo = TiempoProduccion::where('referencia_colchon', $data['referencia_colchon'])->exists();

                    if (!$existeTiempo) {
                        // Obtener el nombre base y la medida del colchón
                        list($nombreBase, $medida) = $this->obtenerNombreBaseYMedida($data['referencia_colchon']);
                        
                        // Buscar una referencia similar basada en el nombre base, pero con diferente medida
                        $tiempoSimilar = TiempoProduccion::where('referencia_colchon', 'LIKE', $nombreBase . '%')
                            ->where('referencia_colchon', '!=', $data['referencia_colchon'])
                            ->first();

                        if ($tiempoSimilar) {
                            // Si encuentra una referencia similar, crea una nueva entrada con los mismos datos
                            $nuevoTiempo = $tiempoSimilar->replicate();
                            $nuevoTiempo->referencia_colchon = $data['referencia_colchon'];
                            $nuevoTiempo->save();
                            Log::info("Creada nueva entrada en TiempoProduccion basada en referencia similar: {$data['referencia_colchon']}");
                        } else {
                            // Si no encuentra una referencia similar, crea un nuevo ColchonSinTiempo
                            ColchonSinTiempo::firstOrCreate(['referencia' => $data['referencia_colchon']]);
                            Log::info("Referencia de colchón sin tiempo de producción: {$data['referencia_colchon']}");
                        }
                    }
                }

                // Añadir al array de inserción solo si los datos son válidos
                if ($this->isValidData($data)) {
                    $dataToInsert[] = $data;
                }
            }

            // Inserta todos los registros válidos al final del loop
            if (!empty($dataToInsert)) {
                Log::info('Inserting data:', $dataToInsert);
                Orden::insert($dataToInsert); // Inserción masiva
            } else {
                Log::info('No valid data to insert.');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en la importación de órdenes: " . $e->getMessage());
            throw $e;
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

    private function obtenerNombreBaseYMedida($referencia)
    {
        // Busca el último espacio en la referencia
        $ultimoEspacio = strrpos($referencia, ' ');
        
        // Si se encuentra un espacio, separa el nombre base y la medida
        if ($ultimoEspacio !== false) {
            $nombreBase = substr($referencia, 0, $ultimoEspacio);
            $medida = substr($referencia, $ultimoEspacio + 1);
            return [$nombreBase, $medida];
        }
        
        // Si no se encuentra un espacio, devuelve la referencia completa como nombre base y medida vacía
        return [$referencia, ''];
    }
}