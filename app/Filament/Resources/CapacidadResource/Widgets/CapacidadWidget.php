<?php

namespace App\Filament\Resources\CapacidadResource\Widgets;

use App\Models\Capacidad;
use Filament\Widgets\Widget;

class CapacidadWidget extends Widget
{
    protected static string $view = 'filament.resources.capacidad-resource.widgets.capacidad-widget';

<<<<<<< HEAD
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
=======
        // Hacer que el widget ocupe todo el ancho
        protected int | string | array $columnSpan = 'full';

        public function getCapacidades()
        {
            // Obtenemos todas las estaciones de trabajo desde la base de datos
            return Capacidad::all();
        }
>>>>>>> main
}
