<?php

namespace App\Filament\Resources\NotificacionResource\Pages;

use App\Filament\Resources\NotificacionResource;
use App\Imports\NotificacionImport;
use App\Models\Notificacion;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ListNotificacions extends ListRecords
{
    protected static string $resource = NotificacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExcelImportAction::make()
                ->processCollectionUsing(function (string $modelClass, Collection $collection) {
                    // Verifica que $collection sea una instancia de Collection
                    if (!$collection instanceof Collection) {
                        throw new \Exception('Expected instance of Illuminate\Support\Collection, got ' . gettype($collection));
                    }

                    // Importar los datos utilizando la clase NotificacionImport
                    $importer = new NotificacionImport();
                    $importer->collection($collection);

                    return $collection; // Retorna la colección si es necesario
                })
                ->use(NotificacionImport::class),
            Actions\CreateAction::make(),
        ];
    }
}