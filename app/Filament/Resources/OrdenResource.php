<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrdenResource\Pages;
use App\Filament\Resources\OrdenResource\RelationManagers;
use App\Models\Orden;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction as TablesExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class OrdenResource extends Resource
{
    protected static ?string $model = Orden::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('orden')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('fecha_puesta_dis_mat'),
                Forms\Components\TextInput::make('numero_material')
                    ->maxLength(255),
                Forms\Components\TextInput::make('pedido_cliente')
                    ->maxLength(255),
                Forms\Components\TextInput::make('pos_pedido_cliente')
                    ->maxLength(255),
                Forms\Components\TextInput::make('cantidad_orden')
                    ->numeric(),
                Forms\Components\TextInput::make('cantidad_buena_notificada')
                    ->numeric(),
                Forms\Components\TextInput::make('Referencia_colchon')
                    ->maxLength(255),
                Forms\Components\TextInput::make('nombre')
                    ->maxLength(255),
                Forms\Components\TextInput::make('denomin_posicion')
                    ->maxLength(255),
                Forms\Components\TextInput::make('estado_sistema')
                    ->maxLength(255),
                Forms\Components\TextInput::make('autor')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('fecha_creacion'),
                Forms\Components\TextInput::make('hora_creacion'),
                Forms\Components\DatePicker::make('fecha_liberac_real'),
                Forms\Components\TextInput::make('modificado_por')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('fecha_fin_notificada'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('orden')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_puesta_dis_mat')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('numero_material')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pedido_cliente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pos_pedido_cliente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cantidad_orden')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cantidad_buena_notificada')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Referencia_colchon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('denomin_posicion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado_sistema')
                    ->searchable(),
                Tables\Columns\TextColumn::make('autor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_creacion')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hora_creacion'),
                Tables\Columns\TextColumn::make('fecha_liberac_real')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('modificado_por')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_fin_notificada')
                    ->date()
                    ->sortable(),
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
                DateRangeFilter::make('fecha_creacion')
                ->label('Fecha de creación')
                ->placeholder('Seleccionar rango de fechas'),
            // Puedes añadir más filtros aquí
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    TablesExportBulkAction::make()->exports([
                        ExcelExport::make('table')->fromTable()->withFilename('Ordenes -'. date('Y-m-d')),
                        ExcelExport::make('form')->fromForm()->withFilename('Ordenes -'. date('Y-m-d')),
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
            'index' => Pages\ListOrdens::route('/'),
            'create' => Pages\CreateOrden::route('/create'),
            'edit' => Pages\EditOrden::route('/{record}/edit'),
        ];
    }
}
