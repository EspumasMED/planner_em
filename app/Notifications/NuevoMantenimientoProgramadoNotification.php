<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\MantenimientoProgramado;
use App\Models\User;
use App\Mail\Mantenimiento;

class NuevoMantenimientoProgramadoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $mantenimientoProgramado;
    protected $creatorUser;

    public function __construct(MantenimientoProgramado $mantenimientoProgramado, User $creatorUser)
    {
        $this->mantenimientoProgramado = $mantenimientoProgramado;
        $this->creatorUser = $creatorUser;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new Mantenimiento($this->mantenimientoProgramado, $this->creatorUser))
                    ->to($notifiable->email);
    }
}