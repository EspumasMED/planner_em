<?php

namespace App\Filament\Resources\TiempoProduccionResource\Widgets;

use App\Models\Orden;
use App\Models\Capacidad;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

class TiempoProduccionWidget extends Widget implements HasForms
{
    // Permite el uso de formularios en el widget
    use \Filament\Forms\Concerns\InteractsWithForms;

    // Define la vista que se usará para renderizar el widget
    protected static string $view = 'filament.resources.tiempo-produccion-resource.widgets.tiempo-produccion-widget';

    // Define el ancho del widget
    protected int | string | array $columnSpan = 'full';

    // Propiedades del widget
    public $startDate; // Fecha de inicio del filtro
    public $endDate;   // Fecha de fin del filtro
    public $includeClientes = true; // Checkbox para incluir clientes en el filtro
    public $includeStock = true;    // Checkbox para incluir stock en el filtro
    public $data = [];  // Datos a mostrar en el widget

    // Método que se ejecuta cuando se monta el widget
    public function mount()
    {
        // Obtiene la última fecha de creación de una orden para establecer la fecha de inicio por defecto
        $lastOrderDate = DB::table('ordenes')
            ->orderBy('fecha_creacion', 'asc') // Ordena las órdenes por fecha de creación en orden ascendente
            ->value('fecha_creacion'); // Obtiene el valor de la última fecha de creación

        // Establece la fecha de inicio como la última fecha de orden o hace 1 semana si no hay órdenes
        $this->startDate = $lastOrderDate ?? now()->subWeek()->toDateString();
        // Establece la fecha de fin como la fecha actual
        $this->endDate = now()->toDateString();

        // Llena el formulario con los valores por defecto
        $this->form->fill([
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'includeClientes' => $this->includeClientes,
            'includeStock' => $this->includeStock,
        ]);

        // Filtra los resultados iniciales
        $this->filterResults();
    }

    // Método para filtrar los resultados según los criterios establecidos
    public function filterResults()
    {
        // Obtiene los datos basados en los filtros actuales
        $this->data = $this->getData();
    }

    // Método para obtener los datos basados en los filtros aplicados
    public function getData()
    {
        // Crea una consulta para obtener órdenes entre las fechas de inicio y fin
        $query = Orden::whereBetween('fecha_creacion', [$this->startDate, $this->endDate]);

        // Aplica filtros adicionales según los criterios
        if ($this->includeClientes && !$this->includeStock) {
            // Si el checkbox de clientes está marcado y el de stock no, solo incluye órdenes con cliente
            $query->whereNotNull('pedido_cliente');
        } elseif (!$this->includeClientes && $this->includeStock) {
            // Si el checkbox de stock está marcado y el de clientes no, solo incluye órdenes sin cliente
            $query->whereNull('pedido_cliente');
        } elseif (!$this->includeClientes && !$this->includeStock) {
            // Si ambos checkboxes están desmarcados, solo se aplica el filtro de fechas
            // No se aplican filtros adicionales
            $query = $query;
        }

        // Calcula el tiempo total de producción por estación basado en la consulta
        $totalTimeByStation = $this->calculateTotalProductionTimeByStation($query);
        Log::info('Total Time by Station:', $totalTimeByStation); // Registra el tiempo total por estación

        // Obtiene las capacidades de las estaciones de trabajo
        $capacidades = Capacidad::all();
        Log::info('Capacidades:', $capacidades->toArray()); // Registra las capacidades obtenidas

        $data = [];
        // Recorre cada capacidad para preparar los datos a mostrar
        foreach ($capacidades as $capacidad) {
            $station = $capacidad->estacion_trabajo; // Nombre de la estación de trabajo
            $stationKey = strtolower(str_replace(' ', '_', $station)); // Convierte el nombre de la estación en una clave en minúsculas con guiones bajos
            $totalMinutes = $totalTimeByStation[$stationKey] ?? 0; // Obtiene el tiempo total para la estación o 0 si no hay datos
            $capacidadDisponible = $capacidad->numero_maquinas * $capacidad->tiempo_jornada; // Calcula la capacidad disponible

            // Agrega los datos al array que será mostrado en el widget
            $data[] = [
                'station' => $station,
                'totalMinutes' => $totalMinutes,
                'capacidadDisponible' => $capacidadDisponible,
            ];
        }

        Log::info('Data for Widget:', $data); // Registra los datos preparados para el widget
        return $data; // Devuelve los datos para mostrar
    }

    // Método para calcular el tiempo total de producción por estación
    protected function calculateTotalProductionTimeByStation($query)
    {
        // Obtiene el total de cantidad por referencia de colchón
        $orders = $query->select('referencia_colchon', DB::raw('SUM(cantidad_orden) as total_quantity'))
            ->groupBy('referencia_colchon')
            ->get();

        // Obtiene los tiempos de producción por referencia de colchón
        $timesByStation = DB::table('tiempos_produccion')
            ->get()
            ->keyBy('referencia_colchon');

        // Inicializa el tiempo total de producción por estación
        $totalTimeByStation = [
            'fileteado_tapas' => 0,
            'fileteado_falsos' => 0,
            'maquina_rufflex' => 0,
            'bordadora' => 0,
            'decorado_falso' => 0,
            'falso_pillow' => 0,
            'encintado' => 0,
            'maquina_plana' => 0,
            'marquillado' => 0,
            'zona_pega' => 0,
            'cierre' => 0,
            'empaque' => 0,
        ];

        // Calcula el tiempo total por estación basado en las órdenes
        foreach ($orders as $order) {
            $referencia = $order->referencia_colchon; // Obtiene la referencia del colchón
            $quantity = (float) $order->total_quantity; // Obtiene la cantidad total de la orden

            // Si existen tiempos para la referencia, se calculan los tiempos totales por estación
            if (isset($timesByStation[$referencia])) {
                $times = $timesByStation[$referencia];

                foreach ($totalTimeByStation as $station => &$time) {
                    $time += $quantity * (float) $times->$station; // Calcula el tiempo total para cada estación
                }
            }
        }

        return $totalTimeByStation; // Devuelve el tiempo total por estación
    }

    // Define el esquema del formulario del widget
    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('startDate') // Campo para seleccionar la fecha de inicio
                ->label('Fecha de inicio') // Etiqueta del campo
                ->default(now()->subMonth()) // Valor por defecto: hace un mes desde ahora
                ->required(), // Campo requerido
            DatePicker::make('endDate') // Campo para seleccionar la fecha de fin
                ->label('Fecha de fin') // Etiqueta del campo
                ->default(now()) // Valor por defecto: fecha actual
                ->required(), // Campo requerido
            Checkbox::make('includeClientes') // Checkbox para incluir clientes
                ->label('Incluir Clientes') // Etiqueta del checkbox
                ->default(true), // Valor por defecto: marcado
            Checkbox::make('includeStock') // Checkbox para incluir stock
                ->label('Incluir Stock') // Etiqueta del checkbox
                ->default(true), // Valor por defecto: marcado
        ];
    }

    // Método que se ejecuta cuando se actualiza una propiedad del formulario
    public function updated($propertyName)
    {
        // Filtra los resultados cada vez que se actualiza una propiedad del formulario
        $this->filterResults();
    }

    // Renderiza la vista del widget
    public function render(): View
    {
        return view(static::$view, [
            'data' => $this->data, // Datos a mostrar
            'startDate' => $this->startDate, // Fecha de inicio
            'endDate' => $this->endDate, // Fecha de fin
            'includeClientes' => $this->includeClientes, // Estado del checkbox de incluir clientes
            'includeStock' => $this->includeStock, // Estado del checkbox de incluir stock
            'form' => $this->form, // Formulario del widget
        ]);
    }
}
