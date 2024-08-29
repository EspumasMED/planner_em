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

        $query = Orden::whereBetween('fecha_creacion', [$this->startDate, $this->endDate]);

        if ($this->includeClientes && !$this->includeStock) {
            $query->whereNotNull('pedido_cliente');
        } elseif (!$this->includeClientes && $this->includeStock) {
            $query->whereNull('pedido_cliente');
        } elseif (!$this->includeClientes && !$this->includeStock) {
            $query = $query;
        }

        $this->clientOrderQuantity = $query->clone()->whereNotNull('pedido_cliente')->sum('cantidad_orden');
        $this->stockOrderQuantity = $query->clone()->whereNull('pedido_cliente')->sum('cantidad_orden');

        $totalOrders = $this->clientOrderQuantity + $this->stockOrderQuantity;
        $this->clientOrderPercentage = $totalOrders > 0 ? ($this->clientOrderQuantity / $totalOrders) * 100 : 0;
        $this->stockOrderPercentage = $totalOrders > 0 ? ($this->stockOrderQuantity / $totalOrders) * 100 : 0;

        $totalProductos = $this->colchonesCantidad + $this->colchonetasCantidad;
        $this->colchonesPercentage = $totalProductos > 0 ? ($this->colchonesCantidad / $totalProductos) * 100 : 0;
        $this->colchonetasPercentage = $totalProductos > 0 ? ($this->colchonetasCantidad / $totalProductos) * 100 : 0;

        $capacidades = Capacidad::all();

        $stationData = [];
        foreach ($capacidades as $capacidad) {
            $station = $capacidad->estacion_trabajo;
            $stationKey = strtolower(str_replace(' ', '_', $station));
            $totalMinutes = $totalTimeByStation[$stationKey] ?? 0;
            $capacidadDisponible = $capacidad->numero_maquinas * $capacidad->tiempo_jornada;

            $stationData[] = [
                'station' => $station,
                'totalMinutes' => $totalMinutes,
                'capacidadDisponible' => $capacidadDisponible,
            ];
        }

        Log::info('Data for Widget:', $stationData);

        return [
            'stationData' => $stationData,
            'totalClosures' => $this->totalClosures,
        ];
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
        ]);
    }
}