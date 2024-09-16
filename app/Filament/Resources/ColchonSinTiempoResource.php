<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ColchonSinTiempoResource\Pages;
use App\Filament\Resources\ColchonSinTiempoResource\RelationManagers;
use App\Models\ColchonSinTiempo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction as TablesExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ColchonSinTiempoResource extends Resource
{
    protected static ?string $model = ColchonSinTiempo::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'Referencias sin tiempos';
    protected static ?string $pluralModelLabel = 'Referencias sin tiempos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('referencia')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('referencia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                TablesExportBulkAction::make()->exports([
                    ExcelExport::make('table')->fromTable()->withFilename('Colchones sin tiempo -'. date('Y-m-d')),
                    ExcelExport::make('form')->fromForm()->withFilename('Colchones sin tiempo -'. date('Y-m-d')),
                ])
                ]),
                
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListColchonSinTiempos::route('/'),
            'create' => Pages\CreateColchonSinTiempo::route('/create'),
            'edit' => Pages\EditColchonSinTiempo::route('/{record}/edit'),
        ];
    }
}
