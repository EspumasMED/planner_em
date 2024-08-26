<?php

namespace App\Filament\Resources\TiempoProduccionResource\Widgets;

use App\Models\Orden;
use App\Models\Capacidad;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

class TiempoProduccionWidget extends Widget
{
    // Especifica la vista que se utilizará para representar el widget
    protected static string $view = 'filament.resources.tiempo-produccion-resource.widgets.tiempo-produccion-widget';

    // Hacer que el widget ocupe todo el ancho
    protected int | string | array $columnSpan = 'full';

    /**
     * Obtiene los datos de tiempos de producción y capacidad.
     *
     * @return array
     */
    public function getData()
    {
        // Obtener los tiempos de producción por estación
        $totalTimeByStation = Orden::calculateTotalProductionTimeByStation();

        // Depuración: Loguear los tiempos de producción obtenidos
        Log::info('Total Time by Station:', $totalTimeByStation);

        // Obtener las capacidades por estación
        $capacidades = Capacidad::all();

        // Depuración: Loguear las capacidades obtenidas
        Log::info('Capacidades:', $capacidades->toArray());

        // Crear un array que combine los tiempos de producción con la capacidad disponible
        $data = [];
        foreach ($capacidades as $capacidad) {
            $station = $capacidad->estacion_trabajo;

            // Transformar el nombre de la estación en un formato que coincida con las claves del array de tiempos
            $stationKey = strtolower(str_replace(' ', '_', $station));

            // Verificar si hay tiempo total registrado para la estación; si no, se establece en 0
            $totalMinutes = $totalTimeByStation[$stationKey] ?? 0;

            // Calcular la capacidad disponible multiplicando el número de máquinas por el tiempo de jornada
            $capacidadEstacion = $capacidad->numero_maquinas * $capacidad->tiempo_jornada;

            // Añadir los datos de la estación al array
            $data[] = [
                'station' => $station,
                'totalMinutes' => $totalMinutes,
                'capacidadDisponible' => $capacidadEstacion,
            ];
        }

        // Depuración: Loguear los datos combinados
        Log::info('Data for Widget:', $data);

        return $data;
    }

    /**
     * Renderiza el widget y pasa los datos necesarios a la vista.
     *
     * @return View
     */
    public function render(): View
    {
        // Obtener los datos para la vista
        $data = $this->getData();

        // Depuración opcional: Muestra los datos para verificar que se están pasando correctamente
        // dd($data);

        // Pasar los datos a la vista
        return view(static::$view, [
            'data' => $data,
        ]);
    }
}
