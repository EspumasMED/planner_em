<?php

namespace App\Filament\Resources\MantenimientoProgramadoResource\Pages;

use App\Filament\Resources\MantenimientoProgramadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\User;
use App\Mail\Mantenimientoedit;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;

class EditMantenimientoProgramado extends EditRecord
{
    protected static string $resource = MantenimientoProgramadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $users = User::all();
        $successCount = 0;
        $failCount = 0;
        $errors = [];

        $editor = Filament::auth()->user();

        foreach ($users as $user) {
            try {
                Log::info("Intentando enviar correo a: " . $user->email);
                Mail::to($user->email)->send(new Mantenimientoedit($this->record, $editor));
                $successCount++;
                Log::info("Correo enviado con éxito a: " . $user->email);
            } catch (\Exception $e) {
                $failCount++;
                $errorMessage = "Error al enviar correo a {$user->email}: " . $e->getMessage();
                Log::error($errorMessage);
                $errors[] = $errorMessage;
            }
        }

        if ($failCount > 0) {
            Notification::make()
                ->title('Advertencia: Algunos correos no se enviaron')
                ->body("Se enviaron {$successCount} notificaciones. {$failCount} fallaron.")
                ->warning()
                ->send();

            Log::error("Detalles de errores en el envío de correos:", $errors);
        } else {
            Notification::make()
                ->title('Mantenimiento actualizado')
                ->body("Se enviaron {$successCount} notificaciones con éxito.")
                ->success()
                ->send();
        }

        Log::info('Mantenimiento programado actualizado', [
            'mantenimiento_id' => $this->record->id,
            'editor_id' => $editor->id,
            'editor_name' => $editor->name,
            'emails_enviados' => $successCount,
            'emails_fallidos' => $failCount,
        ]);
    }
}