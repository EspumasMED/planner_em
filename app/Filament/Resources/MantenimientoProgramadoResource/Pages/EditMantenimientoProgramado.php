<?php

namespace App\Filament\Resources\MantenimientoProgramadoResource\Pages;

use App\Filament\Resources\MantenimientoProgramadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMantenimientoProgramado extends EditRecord
{
    protected static string $resource = MantenimientoProgramadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
