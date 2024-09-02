<?php

namespace App\Imports;

use App\Models\Notificacion;
use App\Models\Orden;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NotificacionImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                Log::info('Processing row:', $row->toArray());

                if (empty($row['orden'])) {
                    Log::info('Skipping row due to empty orden:', $row->toArray());
                    continue;
                }

                $nuevaCantidadNotificada = isset($row['cantidad_buena_notificada_gmein']) ? (int) $row['cantidad_buena_notificada_gmein'] : 0;

                $data = [
                    'notif_orden' => $row['orden'],
                    'notif_fecha_puesta_dis_mat' => $this->parseExcelDate($row['fecha_puesta_dismat']),
                    'notif_numero_material' => $row['numero_material'] ?? null,
                    'notif_pedido_cliente' => $row['pedido_cliente'] ?? null,
                    'notif_pos_pedido_cliente' => $row['pospedido_cliente'] ?? null,
                    'notif_cantidad_orden' => isset($row['cantidad_orden_gmein']) ? (int) $row['cantidad_orden_gmein'] : 0,
                    'notif_cantidad_buena_notificada' => $nuevaCantidadNotificada,
                    'notif_referencia_colchon' => $row['texto_breve_material'] ?? null,
                    'notif_nombre' => $row['nombre'] ?? null,
                    'notif_denomin_posicion' => $row['denominposicion'] ?? null,
                    'notif_estado_sistema' => $row['estado_de_sistema'] ?? null,
                    'notif_autor' => $row['autor'] ?? null,
                    'notif_fecha_creacion' => $this->parseExcelDate($row['fecha_de_creacion']),
                    'notif_hora_creacion' => $this->parseExcelTime($row['hora_creacion']),
                    'notif_fecha_liberac_real' => $this->parseExcelDate($row['fecha_liberacreal']),
                    'notif_modificado_por' => $row['modificado_por'] ?? null,
                    'notif_fecha_fin_notificada' => $this->parseExcelDate($row['fecha_fin_notificada']),
                ];

                // Buscar la notificación existente
                $notificacionExistente = Notificacion::where('notif_orden', $row['orden'])->first();

                $cantidadDiferencial = $nuevaCantidadNotificada;
                if ($notificacionExistente) {
                    $cantidadDiferencial = $nuevaCantidadNotificada - $notificacionExistente->notif_cantidad_buena_notificada;
                }

                // Actualizar o crear la notificación
                $notificacion = Notificacion::updateOrCreate(
                    ['notif_orden' => $row['orden']],
                    $data
                );

                Log::info("Notificación " . ($notificacion->wasRecentlyCreated ? "creada" : "actualizada") . ": {$row['orden']}. Cantidad notificada: $nuevaCantidadNotificada, Cantidad diferencial: $cantidadDiferencial");

                // Actualizar la orden correspondiente solo si la cantidad diferencial no es cero
                if ($cantidadDiferencial != 0) {
                    $orden = Orden::where('orden', $row['orden'])->first();

                    if ($orden) {
                        $nuevaCantidad = $orden->cantidad_orden - $cantidadDiferencial;
                        if ($nuevaCantidad <= 0) {
                            $orden->delete();
                            Log::info("Orden eliminada: {$row['orden']}");
                        } else {
                            $orden->update([
                                'cantidad_orden' => $nuevaCantidad,
                                'cantidad_buena_notificada' => $orden->cantidad_buena_notificada + $cantidadDiferencial
                            ]);
                            Log::info("Orden actualizada: {$row['orden']}. Nueva cantidad: $nuevaCantidad");
                        }
                    } else {
                        Log::warning("No se encontró la orden: {$row['orden']}");
                    }
                } else {
                    Log::info("No se actualizó la orden: {$row['orden']} debido a cantidad diferencial cero");
                }
            }

            DB::commit();
            Log::info('Importación completada con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error durante la importación: ' . $e->getMessage());
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
}