<?php

namespace App\Filament\Resources\MantenimientoProgramadoResource\Pages;

use App\Filament\Resources\MantenimientoProgramadoResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\User;
use App\Mail\Mantenimiento;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Filament\Facades\Filament;

class CreateMantenimientoProgramado extends CreateRecord
{
    protected static string $resource = MantenimientoProgramadoResource::class;

    protected function afterCreate(): void
    {
        $users = User::all();
        $creatorUser = Filament::auth()->user();
        $successCount = 0;
        
        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new Mantenimiento($this->record, $creatorUser));
                $successCount++;
            } catch (\Exception $e) {
                Log::error("Error al enviar correo a {$user->email}: " . $e->getMessage());
            }
        }

        $this->logCreation();

        Notification::make()
            ->title('Mantenimiento creado')
            ->body("Se enviaron {$successCount} correos.")
            ->success()
            ->send();
    }

    private function logCreation(): void
    {
        $userId = Filament::auth()->id();
        $userName = Filament::auth()->user()->name ?? 'Usuario desconocido';

        Log::info('Nuevo mantenimiento programado creado', [
            'mantenimiento_id' => $this->record->id,
            'user_id' => $userId,
            'user_name' => $userName,
        ]);
    }
}