<?php

namespace App\Filament\Resources\CapacidadResource\Widgets;

use App\Models\Capacidad;
use Filament\Widgets\Widget;

class CapacidadWidget extends Widget
{
    protected static string $view = 'filament.resources.capacidad-resource.widgets.capacidad-widget';

        // Hacer que el widget ocupe todo el ancho
        protected int | string | array $columnSpan = 'full';

        public function getCapacidades()
        {
            // Obtenemos todas las estaciones de trabajo desde la base de datos
            return Capacidad::all();
        }
}
