<?php

namespace App\Filament\Resources\OrdenResource\Pages;

use App\Filament\Resources\OrdenResource;
use App\Imports\MyOrdenesImport;
use App\Models\Orden;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ListOrdens extends ListRecords
{
    protected static string $resource = OrdenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExcelImportAction::make()
                ->processCollectionUsing(function (string $modelClass, Collection $collection) {
                    // Verifica que $collection sea una instancia de Collection
                    if (!$collection instanceof Collection) {
                        throw new \Exception('Expected instance of Illuminate\Support\Collection, got ' . gettype($collection));
                    }
    
                    // Procesa cada fila de la colección
                    $collection->each(function ($row) {
                        // Asegúrate de que $row puede ser convertido a array
                        $data = $row instanceof \Illuminate\Contracts\Support\Arrayable ? $row->toArray() : (array) $row;
                        Orden::create($data);
                    });
    
                    return $collection; // Retorna la colección si es necesario
                }),
            //    ->use(MyOrdenesImport::class),
            Actions\CreateAction::make(),
        ];
    }
}
