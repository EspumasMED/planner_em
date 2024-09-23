<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Capacidad extends Model
{
    use HasFactory;

    protected $table = 'capacidades';

    protected $fillable = [
        'estacion_trabajo',
        'numero_maquinas',
        'tiempo_jornada',
        'tiempo_jornada_original', // Añadido el nuevo campo
    ];

    public function mantenimientosProgramados()
    {
        return $this->hasMany(MantenimientoProgramado::class, 'estacion_trabajo', 'estacion_trabajo');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($capacidad) {
            if (is_null($capacidad->tiempo_jornada_original)) {
                $capacidad->tiempo_jornada_original = $capacidad->tiempo_jornada;
            }
        });
    }
}