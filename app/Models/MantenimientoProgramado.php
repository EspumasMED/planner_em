<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MantenimientoProgramado extends Model
{
    use HasFactory;

    protected $table = 'mantenimientos_programados';

    protected $fillable = [
        'fecha_mantenimiento',
        'hora_inicio',
        'hora_fin',
        'estacion_trabajo',
        'numero_maquinas',
        'descripcion',
    ];

    protected $casts = [
        'fecha_mantenimiento' => 'date',
        'hora_inicio' => 'datetime',
        'hora_fin' => 'datetime',
    ];

    public static function getEstacionesTrabajo()
    {
        return Capacidad::pluck('estacion_trabajo', 'estacion_trabajo')->map(function ($estacion) {
            return ucwords(str_replace('_', ' ', $estacion));
        });
    }

    public static function getOpcionesHora()
    {
        $horas = [];
        for ($i = 0; $i < 24; $i++) {
            for ($j = 0; $j < 60; $j += 30) {
                $tiempo = Carbon::createFromTime($i, $j);
                $horas[$tiempo->format('H:i')] = $tiempo->format('h:i A');
            }
        }
        return $horas;
    }

    public static function getOpcionesMaquinas($estacionTrabajo)
    {
        $capacidad = Capacidad::where('estacion_trabajo', $estacionTrabajo)->first();
        if (!$capacidad) {
            return [];
        }
        return array_combine(range(1, $capacidad->numero_maquinas), range(1, $capacidad->numero_maquinas));
    }

    public function getDuracionEnMinutos()
    {
        $inicio = Carbon::parse($this->hora_inicio);
        $fin = Carbon::parse($this->hora_fin);
        return $fin->diffInMinutes($inicio);
    }

    public function getEstacionTrabajoFormateadaAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->estacion_trabajo));
    }
}