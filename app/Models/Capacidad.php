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
    ];

    public function mantenimientosProgramados()
    {
        return $this->hasMany(MantenimientoProgramado::class);
    }
}