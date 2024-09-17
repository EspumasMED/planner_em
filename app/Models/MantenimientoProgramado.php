<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

    protected static function booted()
    {
        static::created(function ($mantenimiento) {
            Log::info("Nuevo mantenimiento creado con ID: {$mantenimiento->id}");
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

    public static function getEstacionesTrabajo()
    {
        return Capacidad::pluck('estacion_trabajo', 'estacion_trabajo')->map(function ($estacion) {
            return ucwords(str_replace('_', ' ', $estacion));
        });
    }

    public static function getOpcionesMaquinas($estacionTrabajo)
    {
        $capacidad = Capacidad::where('estacion_trabajo', $estacionTrabajo)->first();
        if (!$capacidad) {
            Log::warning("No se encontró capacidad para la estación de trabajo: {$estacionTrabajo}");
            return [];
        }
        return array_combine(range(1, $capacidad->numero_maquinas), range(1, $capacidad->numero_maquinas));
    }

    public function actualizarCapacidadSiToca()
    {
        Log::info("Verificando si toca actualizar capacidad para mantenimiento ID: {$this->id}");
        if ($this->fecha_mantenimiento->isToday()) {
            Log::info("Es el día del mantenimiento. Procediendo a actualizar la capacidad.");
            $this->restarCapacidad();
        } else {
            Log::info("No es el día del mantenimiento. No se actualiza la capacidad.");
        }
    }

    public function restarCapacidad()
    {
        Log::info("Iniciando resta de capacidad para mantenimiento ID: {$this->id}");
        
        $tiempoMantenimiento = $this->getDuracionEnMinutos();
        Log::info("Duración del mantenimiento: {$tiempoMantenimiento} minutos");
        
        DB::transaction(function () use ($tiempoMantenimiento) {
            $capacidad = Capacidad::where('estacion_trabajo', $this->estacion_trabajo)->lockForUpdate()->first();
            
            if (!$capacidad) {
                Log::error("No se encontró capacidad para la estación de trabajo: {$this->estacion_trabajo}");
                return;
            }
            
            Log::info("Capacidad actual: {$capacidad->tiempo_jornada} minutos");
            Log::info("Número de máquinas en la estación: {$capacidad->numero_maquinas}");
            Log::info("Número de máquinas afectadas por el mantenimiento: {$this->numero_maquinas}");
            
            $impactoReal = ($tiempoMantenimiento * $this->numero_maquinas) / $capacidad->numero_maquinas;
            Log::info("Impacto real calculado: {$impactoReal} minutos");
            
            // Aseguramos que siempre restemos
            $nuevaJornada = max(0, $capacidad->tiempo_jornada - abs($impactoReal));
            
            Log::info("Nueva jornada calculada: {$nuevaJornada} minutos");
            
            $capacidad->tiempo_jornada = $nuevaJornada;
            $capacidad->save();
            
            Log::info("Capacidad actualizada y guardada. Nueva jornada laboral: {$nuevaJornada} minutos");
        });
    }

    public function getDuracionEnMinutos()
    {
        $inicio = Carbon::parse($this->hora_inicio);
        $fin = Carbon::parse($this->hora_fin);
        
        // Aseguramos que el fin sea siempre después del inicio
        if ($fin->lt($inicio)) {
            $fin->addDay();
        }
        
        $duracion = $fin->diffInMinutes($inicio);
        Log::info("Duración calculada del mantenimiento: {$duracion} minutos");
        return $duracion;
    }

    public function capacidad()
    {
        return $this->belongsTo(Capacidad::class, 'estacion_trabajo', 'estacion_trabajo');
    }

    public function getEstacionTrabajoFormateadaAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->estacion_trabajo));
    }
}