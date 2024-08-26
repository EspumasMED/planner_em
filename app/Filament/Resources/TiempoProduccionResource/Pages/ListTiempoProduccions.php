<?php

namespace App\Filament\Resources\TiempoProduccionResource\Pages;

use App\Filament\Resources\TiempoProduccionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTiempoProduccions extends ListRecords
{
    protected static string $resource = TiempoProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
