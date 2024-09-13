<?php

namespace App\Filament\Resources\TiempoProduccionResource\Widgets;

use App\Models\Orden;
use App\Models\Capacidad;
use App\Models\TiempoProduccion;
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
    use \Filament\Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament.resources.tiempo-produccion-resource.widgets.tiempo-produccion-widget';
    
    protected int | string | array $columnSpan = 'full';

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
    public $metrosLinealesGribetz = 0;
    public $metrosLinealesChina = 0;
    // Nuevas propiedades para el informe detallado
    public $cantidadColchonesCalibre1 = 0;
    public $cantidadColchonesCalibre2 = 0;
    public $cantidadColchonesCalibre3 = 0;
    public $cantidadColchonesCalibre4 = 0;
    public $totalColchonesChina = 0;
    public $totalColchonesGribetz = 0;
    public $distribucionCalibre2China = 0;
    public $distribucionCalibre2Gribetz = 0;
    public $porcentajeCalibre2China = 0;
    public $porcentajeCalibre2Gribetz = 0;

    public $isModalOpen = false;
    public $modalData = [];

    public function mount()
    {
        $lastOrderDate = DB::table('ordenes')
            ->orderBy('fecha_creacion', 'asc')
            ->value('fecha_creacion');

        $this->startDate = $lastOrderDate ?? now()->subWeek()->toDateString();
        $this->endDate = now()->toDateString();

        $this->form->fill([
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'includeClientes' => $this->includeClientes,
            'includeStock' => $this->includeStock,
        ]);

        $this->filterResults();
    }

    public function filterResults()
    {
        $this->data = $this->getData();
    }

    public function getData()
{
    $result = Orden::calculateTotalProductionTimeByStation(
        $this->startDate,
        $this->endDate,
        $this->includeClientes,
        $this->includeStock
    );

    $totalTimeByStation = $result['totalTimeByStation'];
    $this->totalClosures = $result['totalClosures'];
    $this->colchonesCantidad = $result['colchonesCantidad'];
    $this->colchonetasCantidad = $result['colchonetasCantidad'];
    $this->metrosLinealesGribetz = $result['metrosLinealesGribetz'];
    $this->metrosLinealesChina = $result['metrosLinealesChina'];

    // Asignación de datos del informe detallado
    $this->cantidadColchonesCalibre1 = $result['cantidadColchonesCalibre1'];
    $this->cantidadColchonesCalibre2 = $result['cantidadColchonesCalibre2'];
    $this->cantidadColchonesCalibre3 = $result['cantidadColchonesCalibre3'];
    $this->cantidadColchonesCalibre4 = $result['cantidadColchonesCalibre4'];
    $this->totalColchonesChina = $result['totalColchonesChina'];
    $this->totalColchonesGribetz = $result['totalColchonesGribetz'];
    $this->distribucionCalibre2China = $result['distribucionCalibre2China'];
    $this->distribucionCalibre2Gribetz = $result['distribucionCalibre2Gribetz'];
    $this->porcentajeCalibre2China = $result['porcentajeCalibre2China'];
    $this->porcentajeCalibre2Gribetz = $result['porcentajeCalibre2Gribetz'];

    $query = Orden::whereBetween('fecha_creacion', [$this->startDate, $this->endDate]);

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
    }

    $this->clientOrderQuantity = $query->clone()->where(function($q) {
        $q->whereNotNull('pedido_cliente')->where('pedido_cliente', '!=', '');
    })->sum('cantidad_orden');

    $this->stockOrderQuantity = $query->clone()->where(function($q) {
        $q->whereNull('pedido_cliente')->orWhere('pedido_cliente', '');
    })->sum('cantidad_orden');

    $totalOrders = $this->clientOrderQuantity + $this->stockOrderQuantity;
    $this->clientOrderPercentage = $totalOrders > 0 ? ($this->clientOrderQuantity / $totalOrders) * 100 : 0;
    $this->stockOrderPercentage = $totalOrders > 0 ? ($this->stockOrderQuantity / $totalOrders) * 100 : 0;

    $totalProductos = $this->colchonesCantidad + $this->colchonetasCantidad;
    $this->colchonesPercentage = $totalProductos > 0 ? ($this->colchonesCantidad / $totalProductos) * 100 : 0;
    $this->colchonetasPercentage = $totalProductos > 0 ? ($this->colchonetasCantidad / $totalProductos) * 100 : 0;

    $capacidades = Capacidad::all()->keyBy('estacion_trabajo');

    $stationData = [];
    foreach ($capacidades as $station => $capacidad) {
        $stationKey = strtolower(str_replace(' ', '_', $station));
        $capacidadDisponible = $capacidad->numero_maquinas * $capacidad->tiempo_jornada;

        if ($station === 'Acolchadora Gribetz') {
            $stationData[] = [
                'station' => $station,
                'totalMinutes' => round($totalTimeByStation['acolchadora_gribetz']),
                'totalMetrosLineales' => $this->metrosLinealesGribetz,
                'capacidadDisponible' => $capacidadDisponible,
            ];
        } elseif ($station === 'Acolchadora China') {
            $stationData[] = [
                'station' => $station,
                'totalMinutes' => round($totalTimeByStation['acolchadora_china']),
                'totalMetrosLineales' => $this->metrosLinealesChina,
                'capacidadDisponible' => $capacidadDisponible,
            ];
        } else {
            $stationData[] = [
                'station' => $station,
                'totalMinutes' => round($totalTimeByStation[$stationKey] ?? 0),
                'capacidadDisponible' => $capacidadDisponible,
            ];
        }
    }

    // Añade logs aquí para verificar que los datos se están asignando correctamente
    Log::debug("Datos recogidos en el widget:", [
        'metrosLinealesGribetz' => $this->metrosLinealesGribetz,
        'metrosLinealesChina' => $this->metrosLinealesChina,
        'cantidadColchonesCalibre1' => $this->cantidadColchonesCalibre1,
        'cantidadColchonesCalibre2' => $this->cantidadColchonesCalibre2,
        'cantidadColchonesCalibre3' => $this->cantidadColchonesCalibre3,
        'cantidadColchonesCalibre4' => $this->cantidadColchonesCalibre4,
        'totalColchonesChina' => $this->totalColchonesChina,
        'totalColchonesGribetz' => $this->totalColchonesGribetz,
        'distribucionCalibre2China' => $this->distribucionCalibre2China,
        'distribucionCalibre2Gribetz' => $this->distribucionCalibre2Gribetz,
        'porcentajeCalibre2China' => $this->porcentajeCalibre2China,
        'porcentajeCalibre2Gribetz' => $this->porcentajeCalibre2Gribetz,
    ]);

    return [
        'stationData' => $stationData,
        'totalClosures' => $this->totalClosures,
    ];
}

    private function prepareStationData($totalTimeByStation)
{
    $capacidades = Capacidad::all()->keyBy('estacion_trabajo');
    $stationData = [];

    foreach ($capacidades as $station => $capacidad) {
        $stationKey = strtolower(str_replace(' ', '_', $station));
        $capacidadDisponible = $capacidad->numero_maquinas * $capacidad->tiempo_jornada;

        if ($station === 'Acolchadora Gribetz') {
            $stationData[] = [
                'station' => $station,
                'totalMinutes' => round($totalTimeByStation['acolchadora_gribetz']),
                'totalMetrosLineales' => $this->metrosLinealesGribetz,
                'capacidadDisponible' => $capacidadDisponible,
            ];
        } elseif ($station === 'Acolchadora China') {
            $stationData[] = [
                'station' => $station,
                'totalMinutes' => round($totalTimeByStation['acolchadora_china']),
                'totalMetrosLineales' => $this->metrosLinealesChina,
                'capacidadDisponible' => $capacidadDisponible,
            ];
        } else {
            $stationData[] = [
                'station' => $station,
                'totalMinutes' => round($totalTimeByStation[$stationKey] ?? 0),
                'capacidadDisponible' => $capacidadDisponible,
            ];
        }
    }

    return $stationData;
}

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

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['startDate', 'endDate', 'includeClientes', 'includeStock'])) {
            $this->filterResults();
        }
    }

    public function openModal($station, $totalValue, $capacidadDisponible)
    {
        $this->modalData = [
            'station' => $station,
            'totalValue' => $totalValue,
            'capacidadDisponible' => $capacidadDisponible,
            'isAcolchadora' => in_array($station, ['Acolchadora Gribetz', 'Acolchadora China']),
        ];
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function render(): View
    {
        return view(static::$view, [
            'data' => $this->getData(),
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
            'metrosLinealesGribetz' => $this->metrosLinealesGribetz,
            'metrosLinealesChina' => $this->metrosLinealesChina,
            'isModalOpen' => $this->isModalOpen,
            'modalData' => $this->modalData,
            // Nuevos datos para el informe detallado
            'cantidadColchonesCalibre1' => $this->cantidadColchonesCalibre1,
            'cantidadColchonesCalibre2' => $this->cantidadColchonesCalibre2,
            'cantidadColchonesCalibre3' => $this->cantidadColchonesCalibre3,
            'cantidadColchonesCalibre4' => $this->cantidadColchonesCalibre4,
            'totalColchonesChina' => $this->totalColchonesChina,
            'totalColchonesGribetz' => $this->totalColchonesGribetz,
            'distribucionCalibre2China' => $this->distribucionCalibre2China,
            'distribucionCalibre2Gribetz' => $this->distribucionCalibre2Gribetz,
            'porcentajeCalibre2China' => $this->porcentajeCalibre2China,
            'porcentajeCalibre2Gribetz' => $this->porcentajeCalibre2Gribetz,
        ]);
    }
}