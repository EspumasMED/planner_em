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
        $query = Orden::whereBetween('fecha_creacion', [$this->startDate, $this->endDate]);

        // Aplicar filtros basados en los checkboxes
        if ($this->includeClientes && !$this->includeStock) {
            $query->whereNotNull('pedido_cliente');
        } elseif (!$this->includeClientes && $this->includeStock) {
            $query->whereNull('pedido_cliente');
        } elseif (!$this->includeClientes && !$this->includeStock) {
            $query = $query;
        }

        // Calcular cantidades y porcentajes basados en los filtros aplicados
        $this->clientOrderQuantity = $query->clone()->whereNotNull('pedido_cliente')->sum('cantidad_orden');
        $this->stockOrderQuantity = $query->clone()->whereNull('pedido_cliente')->sum('cantidad_orden');

        $totalOrders = $this->clientOrderQuantity + $this->stockOrderQuantity;
        $this->clientOrderPercentage = $totalOrders > 0 ? ($this->clientOrderQuantity / $totalOrders) * 100 : 0;
        $this->stockOrderPercentage = $totalOrders > 0 ? ($this->stockOrderQuantity / $totalOrders) * 100 : 0;

        $orders = $query->select('referencia_colchon', DB::raw('SUM(cantidad_orden) as total_quantity'))
            ->groupBy('referencia_colchon')
            ->get();

        $timesByStation = DB::table('tiempos_produccion')
            ->get()
            ->keyBy('referencia_colchon');

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

        $totalClosures = 0;

        foreach ($orders as $order) {
            $referencia = $order->referencia_colchon;
            $quantity = (float) $order->total_quantity;

            if (isset($timesByStation[$referencia])) {
                $times = $timesByStation[$referencia];
                $totalClosures += $quantity * (float) $times->cierre;

                foreach ($totalTimeByStation as $station => &$time) {
                    $time += $quantity * (float) $times->$station;
                }
            }
        }

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
            'totalClosures' => $totalClosures,
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
        ]);
    }
}