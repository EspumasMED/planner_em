<?php

namespace App\Filament\Resources\MantenimientoProgramadoResource\Pages;

use App\Filament\Resources\MantenimientoProgramadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMantenimientoProgramados extends ListRecords
{
    protected static string $resource = MantenimientoProgramadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
