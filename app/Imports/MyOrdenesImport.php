<?php

namespace App\Imports;

use App\Models\Orden;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class MyOrdenesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Convertir texto de fechas y horas a formato MySQL
            $fechaPuesta = $this->parseDate($row['Fecha de creación']);
            $fechaCreacion = $this->parseDate($row['Fecha de creación']);
            $fechaLiberacion = $this->parseDate($row['Fecha liberac.real']);
            $fechaFinNotificada = $this->parseDate($row['Fecha fin notificada']);
            $horaCreacion = $this->parseTime($row['Hora creación']);

            // Insertar los datos en la base de datos
            Orden::create([
                'orden' => $row['Orden'] ?? null,
                'fecha_puesta' => $fechaPuesta,
                'numero_material' => $row['Número material'] ?? null,
                'pedido_cliente' => $row['Pedido cliente'] ?? null,
                'pos_pedido' => $row['Pos.pedido cliente'] ?? null,
                'cantidad_orden' => isset($row['Cantidad orden']) ? (int) $row['Cantidad orden'] : 0,
                'notificados' => isset($row['Cantidad buena notificada']) ? (int) $row['Cantidad buena notificada'] : 0,
                'referencia_colchon' => $row['Texto breve material'] ?? null,
                'nombre_cliente' => $row['Nombre'] ?? null,
                'denomin_posicion' => $row['Denomin.posición'] ?? null,
                'estado_sistema' => $row['Estado de sistema'] ?? null,
                'autor' => $row['Autor'] ?? null,
                'fecha_creacion' => $fechaCreacion,
                'hora_creacion' => $horaCreacion,
                'fecha_liberacion' => $fechaLiberacion,
                'modificado' => $row['Modificado por'] ?? null,
                'fecha_fin_notificada' => $fechaFinNotificada,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Parse text date to MySQL date format.
     *
     * @param  string|null  $date
     * @return string|null
     */
    private function parseDate($date)
    {
        if (!$date) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse text time to MySQL time format.
     *
     * @param  string|null  $time
     * @return string|null
     */
    private function parseTime($time)
    {
        if (!$time) {
            return null;
        }

        try {
            return Carbon::createFromFormat('H:i:s', $time)->format('H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
}
