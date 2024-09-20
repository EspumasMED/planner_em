<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\MantenimientoProgramado;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Mantenimientoedit extends Mailable
{
    use Queueable, SerializesModels;

    public $mantenimiento;
    public $fechaFormateada;
    public $horaInicio;
    public $horaFin;
    public $editor;

    /**
     * Create a new message instance.
     */
    public function __construct(MantenimientoProgramado $mantenimiento, User $editor)
    {
        $this->mantenimiento = $mantenimiento;
        $this->editor = $editor;
        $this->fechaFormateada = Carbon::parse($mantenimiento->fecha_mantenimiento)->format('Y-m-d');
        $this->horaInicio = $this->formatearHora($mantenimiento->hora_inicio);
        $this->horaFin = $this->formatearHora($mantenimiento->hora_fin);

        Log::info('Mantenimientoedit construido', [
            'mantenimiento_id' => $mantenimiento->id,
            'editor_id' => $editor->id,
            'editor_email' => $editor->email,
            'fecha_formateada' => $this->fechaFormateada,
            'hora_inicio' => $this->horaInicio,
            'hora_fin' => $this->horaFin,
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mantenimiento Programado Actualizado',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.mantenimiento-actualizado',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Formatea la hora con el espacio después de los dos puntos.
     */
    private function formatearHora($hora)
    {
        return Carbon::parse($hora)->format('g : i A');
    }
}