<?php

namespace App\Filament\Resources\ReferenciaResource\Pages;

use App\Filament\Resources\ReferenciaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReferencia extends EditRecord
{
    protected static string $resource = ReferenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
