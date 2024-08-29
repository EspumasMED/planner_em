<?php

namespace App\Filament\Resources\TiempoProduccionResource\Widgets;

use App\Models\Orden; // Importa el modelo Orden para interactuar con la tabla de órdenes
use App\Models\Capacidad; // Importa el modelo Capacidad para obtener las capacidades por estación
use Filament\Forms\Components\DatePicker; // Componente de selección de fecha para los formularios
use Filament\Forms\Components\Checkbox; // Componente de casilla de verificación para los formularios
use Filament\Forms\Contracts\HasForms; // Contrato para formularios en Filament
use Filament\Forms\Form; // Clase Form de Filament
use Filament\Widgets\Widget; // Clase base para widgets en Filament
use Illuminate\Support\Facades\DB; // Facade para interactuar directamente con la base de datos
use Illuminate\Contracts\View\View; // Contrato para vistas
use Illuminate\Support\Facades\Log; // Facade para registro de logs

class TiempoProduccionWidget extends Widget implements HasForms // Define un widget que implementa formularios en Filament
{
    use \Filament\Forms\Concerns\InteractsWithForms; // Trait para interactuar con formularios

    // Define la vista que se utilizará para renderizar el widget
    protected static string $view = 'filament.resources.tiempo-produccion-resource.widgets.tiempo-produccion-widget';
    
    protected int | string | array $columnSpan = 'full'; // Define el tamaño de la columna que ocupará el widget

    // Variables públicas para mantener el estado del widget
    public $clientOrderQuantity = 0; // Cantidad de órdenes de clientes
    public $stockOrderQuantity = 0; // Cantidad de órdenes de stock
    public $clientOrderPercentage = 0; // Porcentaje de órdenes de clientes
    public $stockOrderPercentage = 0; // Porcentaje de órdenes de stock
    public $startDate; // Fecha de inicio para filtrar las órdenes
    public $endDate; // Fecha de fin para filtrar las órdenes
    public $includeClientes = true; // Variable para incluir pedidos de clientes
    public $includeStock = true; // Variable para incluir pedidos de stock
    public $data = []; // Array para almacenar los datos procesados
    public $totalClosures = 0; // Total de cierres calculados

    // Método mount: se ejecuta al inicializar el widget
    public function mount()
    {
        // Obtiene la fecha de creación de la primera orden
        $lastOrderDate = DB::table('ordenes')
            ->orderBy('fecha_creacion', 'asc')
            ->value('fecha_creacion');

        // Establece las fechas de inicio y fin para los filtros
        $this->startDate = $lastOrderDate ?? now()->subWeek()->toDateString(); // Por defecto, una semana atrás
        $this->endDate = now()->toDateString(); // Fecha actual

        // Rellena el formulario con las fechas y opciones predeterminadas
        $this->form->fill([
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'includeClientes' => $this->includeClientes,
            'includeStock' => $this->includeStock,
        ]);

        // Filtra los resultados iniciales
        $this->filterResults();
    }

    // Método para filtrar los resultados basados en los filtros seleccionados
    public function filterResults()
    {
        // Obtiene los datos filtrados
        $this->data = $this->getData();
    }

    // Método para obtener los datos de producción filtrados
    public function getData()
    {
        // Llama al método estático de Orden para calcular el tiempo total de producción por estación
        $result = Orden::calculateTotalProductionTimeByStation(
            $this->startDate,
            $this->endDate,
            $this->includeClientes,
            $this->includeStock
        );

        $totalTimeByStation = $result['totalTimeByStation']; // Tiempo total por estación
        $this->totalClosures = $result['totalClosures']; // Total de cierres calculados

        // Crea una consulta base filtrada por las fechas de inicio y fin
        $query = Orden::whereBetween('fecha_creacion', [$this->startDate, $this->endDate]);

        // Filtra según si se incluyen pedidos de clientes o de stock
        if ($this->includeClientes && !$this->includeStock) {
            $query->whereNotNull('pedido_cliente'); // Solo pedidos de clientes
        } elseif (!$this->includeClientes && $this->includeStock) {
            $query->whereNull('pedido_cliente'); // Solo pedidos de stock
        } elseif (!$this->includeClientes && !$this->includeStock) {
            $query = $query; // No se aplican filtros adicionales
        }

        // Calcula la cantidad de órdenes de clientes y de stock
        $this->clientOrderQuantity = $query->clone()->whereNotNull('pedido_cliente')->sum('cantidad_orden');
        $this->stockOrderQuantity = $query->clone()->whereNull('pedido_cliente')->sum('cantidad_orden');

        // Calcula los porcentajes de órdenes de clientes y de stock
        $totalOrders = $this->clientOrderQuantity + $this->stockOrderQuantity;
        $this->clientOrderPercentage = $totalOrders > 0 ? ($this->clientOrderQuantity / $totalOrders) * 100 : 0;
        $this->stockOrderPercentage = $totalOrders > 0 ? ($this->stockOrderQuantity / $totalOrders) * 100 : 0;

        // Obtiene las capacidades de las estaciones de trabajo
        $capacidades = Capacidad::all();

        $stationData = []; // Inicializa un array para almacenar los datos por estación
        foreach ($capacidades as $capacidad) {
            $station = $capacidad->estacion_trabajo; // Estación de trabajo
            $stationKey = strtolower(str_replace(' ', '_', $station)); // Convierte el nombre de la estación a formato de clave
            $totalMinutes = $totalTimeByStation[$stationKey] ?? 0; // Tiempo total de producción en minutos
            $capacidadDisponible = $capacidad->numero_maquinas * $capacidad->tiempo_jornada; // Capacidad disponible en minutos

            // Agrega los datos de la estación al array
            $stationData[] = [
                'station' => $station,
                'totalMinutes' => $totalMinutes,
                'capacidadDisponible' => $capacidadDisponible,
            ];
        }

        // Registra los datos de la estación en los logs
        Log::info('Data for Widget:', $stationData);

        // Retorna los datos procesados
        return [
            'stationData' => $stationData,
            'totalClosures' => $this->totalClosures,
        ];
    }

    // Método para definir el esquema del formulario
    protected function getFormSchema(): array
    {
        return [
            // Selector de fecha de inicio
            DatePicker::make('startDate')
                ->label('Fecha de inicio')
                ->default(now()->subMonth()) // Por defecto, un mes atrás
                ->required(),

            // Selector de fecha de fin
            DatePicker::make('endDate')
                ->label('Fecha de fin')
                ->default(now()) // Por defecto, fecha actual
                ->required(),

            // Casilla de verificación para incluir pedidos de clientes
            Checkbox::make('includeClientes')
                ->label('Incluir Clientes'),

            // Casilla de verificación para incluir pedidos de stock
            Checkbox::make('includeStock')
                ->label('Incluir Stock'),
        ];
    }

    // Método que se ejecuta cuando una propiedad del formulario es actualizada
    public function updated($propertyName)
    {
        // Si se actualizan las propiedades que afectan el filtro, se recalculan los resultados
        if (in_array($propertyName, ['startDate', 'endDate', 'includeClientes', 'includeStock'])) {
            $this->filterResults();
        }
    }

    // Método render para renderizar la vista del widget
    public function render(): View
    {
        return view(static::$view, [
            'data' => $this->data, // Pasa los datos al renderizado
            'startDate' => $this->startDate, // Pasa la fecha de inicio
            'endDate' => $this->endDate, // Pasa la fecha de fin
            'includeClientes' => $this->includeClientes, // Pasa la opción de incluir clientes
            'includeStock' => $this->includeStock, // Pasa la opción de incluir stock
            'clientOrderQuantity' => $this->clientOrderQuantity, // Pasa la cantidad de órdenes de clientes
            'stockOrderQuantity' => $this->stockOrderQuantity, // Pasa la cantidad de órdenes de stock
            'clientOrderPercentage' => $this->clientOrderPercentage, // Pasa el porcentaje de órdenes de clientes
            'stockOrderPercentage' => $this->stockOrderPercentage, // Pasa el porcentaje de órdenes de stock
            'totalClosures' => $this->totalClosures, // Pasa el total de cierres
        ]);
    }
}
