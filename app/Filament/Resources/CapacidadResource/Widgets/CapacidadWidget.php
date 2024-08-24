<?php

namespace App\Filament\Resources\CapacidadResource\Widgets;

use App\Models\Capacidad;
use Filament\Widgets\Widget;

class CapacidadWidget extends Widget
{
    protected static string $view = 'filament.resources.capacidad-resource.widgets.capacidad-widget';

    public function viewData(): array
    {
        // Consulta la información de la base de datos y realiza el cálculo
        $capacities = Capacidad::all()->map(function ($capacidad) {
            $capacidad->result = $capacidad->numero_maquinas * $capacidad->numero_maquinas;
            return $capacidad;
        });

        // Depurar para verificar los datos
        dd($capacities); 

        // Asegúrate de retornar con el nombre correcto de la variable
        return [
            'capacities' => $capacities,
        ];
    }
}
