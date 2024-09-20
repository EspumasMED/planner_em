<?php

namespace App\Console\Commands;

use App\Mail\Mantenimiento;
use App\Models\User;
use App\Models\MantenimientoProgramado;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class Testemails extends Command
{
    protected $signature = 'test:emails';

    protected $description = 'Envía un correo de prueba de mantenimiento programado a todos los usuarios';

    public function handle()
    {
        $users = User::all();
        if ($users->isEmpty()) {
            $this->error('No se encontraron usuarios en la base de datos.');
            return 1;
        }

        $this->info('Se encontraron ' . $users->count() . ' usuarios.');

        // Crear un mantenimiento de prueba
        $mantenimiento = MantenimientoProgramado::create([
            'fecha_mantenimiento' => now()->addDays(7)->toDateString(),
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'estacion_trabajo' => 'Estación de prueba',
            'numero_maquinas' => 5,
            'descripcion' => 'Este es un mantenimiento de prueba para verificar el envío de correos.',
        ]);

        // Seleccionar un usuario como creador para la prueba
        $creatorUser = $users->first();

        $successCount = 0;
        $failCount = 0;

        foreach ($users as $user) {
            $this->info('Enviando correo a: ' . $user->email);

            try {
                Mail::to($user->email)->send(new Mantenimiento($mantenimiento, $creatorUser));
                $this->info('Correo enviado con éxito a: ' . $user->email);
                $successCount++;
            } catch (\Exception $e) {
                $this->error('Error al enviar el correo a ' . $user->email . ': ' . $e->getMessage());
                $failCount++;
            }

            // Añadir un pequeño retraso entre envíos para evitar sobrecarga
            sleep(1);
        }

        $this->info("Resumen: {$successCount} correos enviados con éxito, {$failCount} fallidos.");
        
        // Eliminar el mantenimiento de prueba
        $mantenimiento->delete();
        $this->info("Mantenimiento de prueba eliminado.");

        return $failCount > 0 ? 1 : 0;
    }
}