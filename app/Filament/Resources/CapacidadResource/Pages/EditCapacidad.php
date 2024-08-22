<?php

namespace App\Filament\Resources\CapacidadResource\Pages;

use App\Filament\Resources\CapacidadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCapacidad extends EditRecord
{
    protected static string $resource = CapacidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
