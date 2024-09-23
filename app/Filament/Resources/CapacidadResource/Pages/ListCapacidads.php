<?php

namespace App\Filament\Resources\CapacidadResource\Pages;

use App\Filament\Resources\CapacidadResource;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\CapacidadResource\Widgets\CapacidadSliderWidget;
use Filament\Actions;

class ListCapacidads extends ListRecords
{
    protected static string $resource = CapacidadResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            CapacidadSliderWidget::class,
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

}