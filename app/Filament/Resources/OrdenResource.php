<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrdenResource\Pages;
use App\Filament\Resources\OrdenResource\RelationManagers;
use App\Models\Orden;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class OrdenResource extends Resource
{
    protected static ?string $model = Orden::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('orden')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('fecha_puesta'),
                Forms\Components\TextInput::make('numero_material')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('pedido_cliente')
                    ->maxLength(255),
                Forms\Components\TextInput::make('pos_pedido')
                    ->maxLength(255),
                Forms\Components\TextInput::make('cantidad_orden')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('notificados')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('referencia_colchon')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nombre_cliente')
                    ->maxLength(255),
                Forms\Components\TextInput::make('denomin_posicion')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('estado_sistema')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('autor')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('fecha_creacion')
                    ->required(),
                Forms\Components\TextInput::make('hora_creacion')
                    ->required(),
                Forms\Components\DatePicker::make('fecha_liberacion'),
                Forms\Components\TextInput::make('modificado')
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
                Tables\Columns\TextColumn::make('fecha_puesta')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('numero_material')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pedido_cliente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pos_pedido')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cantidad_orden')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notificados')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('referencia_colchon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre_cliente')
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
                Tables\Columns\TextColumn::make('fecha_liberacion')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('modificado')
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()->exports([
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
