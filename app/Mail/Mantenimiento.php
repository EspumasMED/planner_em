<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\MantenimientoProgramado;
use App\Models\User;
use Carbon\Carbon;

class Mantenimiento extends Mailable
{
    use Queueable, SerializesModels;

    public $mantenimiento;
    public $fechaFormateada;
    public $horaInicio;
    public $horaFin;
    public $emailCreador;

    public function __construct(MantenimientoProgramado $mantenimiento, User $creador)
    {
        $this->mantenimiento = $mantenimiento;
        
        $this->fechaFormateada = Carbon::parse($mantenimiento->fecha_mantenimiento)->format('Y-m-d');
        $this->horaInicio = Carbon::parse($mantenimiento->hora_inicio)->format('g:i A');
        $this->horaFin = Carbon::parse($mantenimiento->hora_fin)->format('g:i A');
        $this->emailCreador = $creador->email;
    }

    public function build()
    {
        return $this->view('mails.mantenimiento')
                    ->subject('Nuevo Mantenimiento Programado');
    }
}