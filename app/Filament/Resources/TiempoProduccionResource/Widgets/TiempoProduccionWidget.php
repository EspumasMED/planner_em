<?php

namespace App\Filament\Resources\TiempoProduccionResource\Widgets;

use App\Models\Orden;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class TiempoProduccionWidget extends Widget
{
    // Especifica la vista que se utilizará para representar el widget
    protected static string $view = 'filament.resources.tiempo-produccion-resource.widgets.tiempo-produccion-widget';

    // Hacer que el widget ocupe todo el ancho
    protected int | string | array $columnSpan = 'full';
    /**
     * Renderiza el widget y pasa los datos necesarios a la vista.
     *
     * @return View
     */
    public function render(): View
    {
        // Llama al método 'calculateTotalProductionTimeByStation' del modelo Orden para obtener el tiempo total de producción por estación
        $totalTimeByStation = Orden::calculateTotalProductionTimeByStation();

        // // Inicializa un array para almacenar los tiempos en horas y minutos
        // $timeInHoursAndMinutes = [];

        // // Convierte los tiempos de minutos a horas y minutos para cada estación de trabajo
        // foreach ($totalTimeByStation as $station => $totalMinutes) {
        //     // Calcula las horas dividiendo el total de minutos por 60
        //     $hours = intdiv($totalMinutes, 60);
        //     // Calcula los minutos restantes utilizando el operador módulo
        //     $minutes = $totalMinutes % 60;
        //     // Almacena el tiempo convertido en el array con la clave de la estación correspondiente
        //     $timeInHoursAndMinutes[$station] = [
        //         'hours' => $hours,
        //         'minutes' => $minutes,
        //     ];
        // }

        // // Retorna la vista con los datos calculados, pasando el array 'timeInHoursAndMinutes' a la vista
        // return view(static::$view, [
        //     'totalTimeByStation' => $timeInHoursAndMinutes,
        // ]);
        // Pasar los datos a la vista sin convertirlos a horas y minutos
        return view(static::$view, [
            'totalTimeByStation' => $totalTimeByStation,
        ]);
    }
}
