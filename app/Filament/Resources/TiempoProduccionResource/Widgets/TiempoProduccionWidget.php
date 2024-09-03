<?php

namespace App\Filament\Resources\TiempoProduccionResource\Widgets;

// Importa los modelos 'Orden' y 'Capacidad'
use App\Models\Orden;
use App\Models\Capacidad;
// Importa los componentes de formularios de Filament
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
// Importa la clase 'Widget' de Filament
use Filament\Widgets\Widget;
// Importa las clases de Laravel necesarias para consultas y logging
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

// Define una clase que extiende 'Widget' e implementa 'HasForms'
class TiempoProduccionWidget extends Widget implements HasForms
{
    // Usa el trait de Filament para gestionar formularios
    use \Filament\Forms\Concerns\InteractsWithForms;

    // Define la vista que se utilizará para renderizar este widget
    protected static string $view = 'filament.resources.tiempo-produccion-resource.widgets.tiempo-produccion-widget';
    
    // Define el número de columnas que ocupará el widget en el layout de la página
    protected int | string | array $columnSpan = 'full';

    // Define propiedades públicas para almacenar varios valores del widget
    public $clientOrderQuantity = 0;
    public $stockOrderQuantity = 0;
    public $clientOrderPercentage = 0;
    public $stockOrderPercentage = 0;
    public $startDate;
    public $endDate;
    public $includeClientes = true;
    public $includeStock = true;
    public $data = [];
    public $totalClosures = 0;
    public $colchonesCantidad = 0;
    public $colchonetasCantidad = 0;
    public $colchonesPercentage = 0;
    public $colchonetasPercentage = 0;

    // Nuevas propiedades para gestionar un modal
    public $isModalOpen = false;
    public $modalData = [];

    // Método de inicialización que se ejecuta cuando se monta el componente
    public function mount()
    {
        // Obtiene la fecha de la primera orden registrada en la base de datos
        $lastOrderDate = DB::table('ordenes')
            ->orderBy('fecha_creacion', 'asc')
            ->value('fecha_creacion');

        // Establece las fechas de inicio y fin, por defecto usando la fecha de la primera orden o una semana atrás
        $this->startDate = $lastOrderDate ?? now()->subWeek()->toDateString();
        $this->endDate = now()->toDateString();

        // Llena el formulario con las fechas de inicio, fin y los checkboxes
        $this->form->fill([
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'includeClientes' => $this->includeClientes,
            'includeStock' => $this->includeStock,
        ]);

        // Filtra los resultados iniciales
        $this->filterResults();
    }

    // Método para filtrar los resultados de acuerdo a los parámetros actuales
    public function filterResults()
    {
        // Llama al método 'getData' para obtener los datos filtrado
        $this->data = $this->getData();
    }

    // Método que obtiene y calcula los datos basados en las fechas y filtros seleccionados
    public function getData()
    {
        // Llama a un método en el modelo 'Orden' para calcular el tiempo total de producción por estación
        $result = Orden::calculateTotalProductionTimeByStation(
            $this->startDate,
            $this->endDate,
            $this->includeClientes,
            $this->includeStock
        );

        // Almacena los resultados del cálculo
        $totalTimeByStation = $result['totalTimeByStation'];
        $this->totalClosures = $result['totalClosures'];
        $this->colchonesCantidad = $result['colchonesCantidad'];
        $this->colchonetasCantidad = $result['colchonetasCantidad'];

        // Crea una consulta para las órdenes filtradas por fecha
        $query = Orden::whereBetween('fecha_creacion', [$this->startDate, $this->endDate]);

        // Filtra la consulta si se selecciona incluir o excluir clientes y stock
        if ($this->includeClientes && !$this->includeStock) {
            $query->where(function($q) {
                $q->whereNotNull('pedido_cliente')
                  ->where('pedido_cliente', '!=', '');
            });
        } elseif (!$this->includeClientes && $this->includeStock) {
            $query->where(function($q) {
                $q->whereNull('pedido_cliente')
                  ->orWhere('pedido_cliente', '');
            });
        } elseif (!$this->includeClientes && !$this->includeStock) {
            $query = $query;
        }

        // Calcula la cantidad total de órdenes de clientes
        $this->clientOrderQuantity = $query->clone()->where(function($q) {
            $q->whereNotNull('pedido_cliente')->where('pedido_cliente', '!=', '');
        })->sum('cantidad_orden');

        // Calcula la cantidad total de órdenes de stock
        $this->stockOrderQuantity = $query->clone()->where(function($q) {
            $q->whereNull('pedido_cliente')->orWhere('pedido_cliente', '');
        })->sum('cantidad_orden');

        // Calcula los porcentajes de órdenes de clientes y stock
        $totalOrders = $this->clientOrderQuantity + $this->stockOrderQuantity;
        $this->clientOrderPercentage = $totalOrders > 0 ? ($this->clientOrderQuantity / $totalOrders) * 100 : 0;
        $this->stockOrderPercentage = $totalOrders > 0 ? ($this->stockOrderQuantity / $totalOrders) * 100 : 0;

        // Calcula los porcentajes de colchones y colchonetas
        $totalProductos = $this->colchonesCantidad + $this->colchonetasCantidad;
        $this->colchonesPercentage = $totalProductos > 0 ? ($this->colchonesCantidad / $totalProductos) * 100 : 0;
        $this->colchonetasPercentage = $totalProductos > 0 ? ($this->colchonetasCantidad / $totalProductos) * 100 : 0;

        // Obtiene todas las capacidades de las estaciones de trabajo
        $capacidades = Capacidad::all();

        // Prepara los datos para cada estación de trabajo
        $stationData = [];
        foreach ($capacidades as $capacidad) {
            $station = $capacidad->estacion_trabajo;
            $stationKey = strtolower(str_replace(' ', '_', $station));
            $totalMinutes = $totalTimeByStation[$stationKey] ?? 0;
            $capacidadDisponible = $capacidad->numero_maquinas * $capacidad->tiempo_jornada;

            // Almacena los datos de la estación
            $stationData[] = [
                'station' => $station,
                'totalMinutes' => $totalMinutes,
                'capacidadDisponible' => $capacidadDisponible,
            ];
        }

        // Registra la información de las estaciones en los logs
        Log::info('Data for Widget:', $stationData);

        // Devuelve los datos de las estaciones y el total de cierres
        return [
            'stationData' => $stationData,
            'totalClosures' => $this->totalClosures,
        ];
    }

    // Define el esquema del formulario con los componentes DatePicker y Checkbox
    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('startDate')
                ->label('Fecha de inicio')
                ->default(now()->subMonth())
                ->required(),

            DatePicker::make('endDate')
                ->label('Fecha de fin')
                ->default(now())
                ->required(),

            Checkbox::make('includeClientes')
                ->label('Incluir Clientes'),

            Checkbox::make('includeStock')
                ->label('Incluir Stock'),
        ];
    }

    // Método que se llama cuando se actualiza alguna propiedad del formulario
    public function updated($propertyName)
    {
        // Si la propiedad actualizada es relevante para el filtrado, filtra los resultados de nuevo
        if (in_array($propertyName, ['startDate', 'endDate', 'includeClientes', 'includeStock'])) {
            $this->filterResults();
        }
    }

    // Método para abrir el modal con datos específicos de una estación
    public function openModal($station, $totalMinutes, $capacidadDisponible)
    {
        $this->modalData = [
            'station' => $station,
            'totalMinutes' => $totalMinutes,
            'capacidadDisponible' => $capacidadDisponible,
        ];
        $this->isModalOpen = true;
    }

    // Método para cerrar el modal
    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    // Método que renderiza la vista del widget, pasando los datos necesarios
    public function render(): View
    {
        return view(static::$view, [
            'data' => $this->data,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'includeClientes' => $this->includeClientes,
            'includeStock' => $this->includeStock,
            'clientOrderQuantity' => $this->clientOrderQuantity,
            'stockOrderQuantity' => $this->stockOrderQuantity,
            'clientOrderPercentage' => $this->clientOrderPercentage,
            'stockOrderPercentage' => $this->stockOrderPercentage,
            'totalClosures' => $this->totalClosures,
            'colchonesCantidad' => $this->colchonesCantidad,
            'colchonetasCantidad' => $this->colchonetasCantidad,
            'colchonesPercentage' => $this->colchonesPercentage,
            'colchonetasPercentage' => $this->colchonetasPercentage,
            'isModalOpen' => $this->isModalOpen,
            'modalData' => $this->modalData,
        ]);
    }
}
