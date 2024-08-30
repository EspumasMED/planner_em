<?php

namespace App\Filament\Resources\TiempoProduccionResource\Pages;

use App\Filament\Resources\TiempoProduccionResource;
use App\Imports\TiempoProduccionImport;
use App\Models\TiempoProduccion;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Collection;

class ListTiempoProduccions extends ListRecords
{
    protected static string $resource = TiempoProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExcelImportAction::make()
                ->processCollectionUsing(function (string $modelClass, Collection $collection) {
                    if (!$collection instanceof Collection) {
                        throw new \Exception('Expected instance of Illuminate\Support\Collection, got ' . gettype($collection));
                    }

                    $importer = new TiempoProduccionImport();
                    $importer->collection($collection);

                    return $collection;
                })
                ->use(TiempoProduccionImport::class),
            Actions\CreateAction::make(),
        ];
    }
}