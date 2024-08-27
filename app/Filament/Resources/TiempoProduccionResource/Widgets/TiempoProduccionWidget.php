<?php

namespace App\Filament\Resources\TiempoProduccionResource\Widgets;

use App\Models\Orden;
use App\Models\Capacidad;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

class TiempoProduccionWidget extends Widget implements HasForms
{
    use \Filament\Forms\Concerns\InteractsWithForms; // Usa el trait para gestionar formularios

    // Define la vista que se usará para renderizar el widget
    protected static string $view = 'filament.resources.tiempo-produccion-resource.widgets.tiempo-produccion-widget';

    // Define el ancho del widget en la interfaz
    protected int | string | array $columnSpan = 'full';

    // Propiedades públicas para manejar las fechas y los datos
    public $startDate;
    public $endDate;
    public $data = [];

    // Método que se ejecuta cuando el widget es inicializado
    public function mount()
{
    // Obtén la última fecha de la columna 'fecha_creacion' de la tabla 'ordenes'
    $lastOrderDate = DB::table('ordenes')
        ->orderBy('fecha_creacion', 'desc')
        ->value('fecha_creacion');

    // Si existe una fecha, úsala como startDate; de lo contrario, usa una semana atrás
    $this->startDate = $lastOrderDate ?? now()->subWeek()->toDateString();

    // Establece endDate a la fecha actual
    $this->endDate = now()->toDateString();

    // Inicializa el formulario con las fechas predeterminadas
    $this->form->fill([
        'startDate' => $this->startDate,
        'endDate' => $this->endDate,
    ]);

    // Carga los resultados iniciales
    $this->filterResults();
}

    // Método para actualizar los datos del widget según el rango de fechas
    public function filterResults()
    {
        $this->data = $this->getData(); // Llama a getData para obtener los datos filtrados
    }

    // Método para obtener los datos procesados
    public function getData()
    {
        // Calcula el tiempo total de producción por estación en el rango de fechas especificado
        $totalTimeByStation = Orden::calculateTotalProductionTimeByStation($this->startDate, $this->endDate);
        Log::info('Total Time by Station:', $totalTimeByStation); // Registra los tiempos totales

        // Obtiene la capacidad de todas las estaciones
        $capacidades = Capacidad::all();
        Log::info('Capacidades:', $capacidades->toArray()); // Registra las capacidades

        // Crea un array para almacenar los datos procesados
        $data = [];
        foreach ($capacidades as $capacidad) {
            $station = $capacidad->estacion_trabajo; // Obtiene el nombre de la estación
            $stationKey = strtolower(str_replace(' ', '_', $station)); // Normaliza el nombre de la estación
            $totalMinutes = $totalTimeByStation[$stationKey] ?? 0; // Obtiene el tiempo total para la estación
            $capacidadDisponible = $capacidad->numero_maquinas * $capacidad->tiempo_jornada; // Calcula la capacidad disponible

            $data[] = [
                'station' => $station,
                'totalMinutes' => $totalMinutes,
                'capacidadDisponible' => $capacidadDisponible,
            ];
        }

        Log::info('Data for Widget:', $data); // Registra los datos procesados
        return $data;
    }

    // Define el esquema del formulario con campos de selección de fechas
    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('startDate') // Campo para seleccionar la fecha de inicio
                ->label('Fecha de inicio')
                ->default(now()->subMonth()) // Valor predeterminado: primer día del mes pasado
                ->required(),
            DatePicker::make('endDate') // Campo para seleccionar la fecha de fin
                ->label('Fecha de fin')
                ->default(now()) // Valor predeterminado: fecha actual
                ->required(),
        ];
    }

    // Método que se llama cuando se actualiza una propiedad del formulario
    public function updated($propertyName)
    {
        if ($propertyName === 'startDate' || $propertyName === 'endDate') {
            $this->filterResults(); // Filtra resultados si se actualiza alguna de las fechas
        }
    }

    // Método para renderizar la vista del widget
    public function render(): View
    {
        return view(static::$view, [
            'data' => $this->data, // Datos procesados para mostrar en la vista
            'startDate' => $this->startDate, // Fecha de inicio
            'endDate' => $this->endDate, // Fecha de fin
            'form' => $this->form, // El formulario para mostrar en la vista
        ]);
    }
}
