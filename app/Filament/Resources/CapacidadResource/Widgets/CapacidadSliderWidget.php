<?php

namespace App\Filament\Resources\CapacidadResource\Widgets;

use App\Models\Capacidad;
use Filament\Widgets\Widget;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class CapacidadSliderWidget extends Widget
{
    protected static string $view = 'filament.resources.capacidad-resource.widgets.capacidad-widget';
    
    public int | string | array $columnSpan = 'full';

    public $porcentajeOptimismo = 100;
    public $capacidades = [];

    public function mount()
    {
        $this->refreshCapacidades();
        $this->inicializarPorcentajeOptimismo();
    }

    private function inicializarPorcentajeOptimismo()
    {
        if (count($this->capacidades) > 0) {
            $primerCapacidad = $this->capacidades[0];
            $this->porcentajeOptimismo = round(($primerCapacidad['tiempo_jornada'] / $primerCapacidad['tiempo_jornada_original']) * 100);
        }
    }

    #[On('refreshCapacidades')]
    public function refreshCapacidades()
    {
        $this->capacidades = Capacidad::all()->map(function ($capacidad) {
            $capacidad->tiempo_jornada_ajustado = $this->calcularTiempoJornadaAjustado($capacidad->tiempo_jornada_original);
            return $capacidad;
        })->toArray();
    }

    public function updatedPorcentajeOptimismo()
    {
        $this->refreshCapacidades();
    }

    private function calcularTiempoJornadaAjustado($tiempoJornadaOriginal)
    {
        return round($tiempoJornadaOriginal * ($this->porcentajeOptimismo / 100));
    }

    public function actualizarTablaCapacidad()
    {
        DB::transaction(function () {
            foreach ($this->capacidades as $capacidad) {
                Capacidad::where('id', $capacidad['id'])->update([
                    'tiempo_jornada' => $capacidad['tiempo_jornada_ajustado']
                ]);
            }
        });

        $this->refreshCapacidades();
        // $this->notify('success', 'Tabla de capacidades actualizada correctamente.');
        
        // Recargar la página
        return redirect(request()->header('Referer'));
    }
}