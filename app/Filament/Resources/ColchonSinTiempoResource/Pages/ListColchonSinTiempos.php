<?php

namespace App\Filament\Resources\ColchonSinTiempoResource\Pages;

use App\Filament\Resources\ColchonSinTiempoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListColchonSinTiempos extends ListRecords
{
    protected static string $resource = ColchonSinTiempoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
