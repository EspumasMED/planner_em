<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificacionResource\Pages;
use App\Filament\Resources\NotificacionResource\RelationManagers;
use App\Models\Notificacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction as TablesExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class NotificacionResource extends Resource
{
    protected static ?string $model = Notificacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Ordenes Notificadas';
    protected static ?string $pluralModelLabel = 'Ordenes Notificadas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('notif_orden')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('notif_fecha_puesta_dis_mat'),
                Forms\Components\TextInput::make('notif_numero_material')
                    ->maxLength(255),
                Forms\Components\TextInput::make('notif_pedido_cliente')
                    ->maxLength(255),
                Forms\Components\TextInput::make('notif_pos_pedido_cliente')
                    ->maxLength(255),
                Forms\Components\TextInput::make('notif_cantidad_orden')
                    ->numeric(),
                Forms\Components\TextInput::make('notif_cantidad_buena_notificada')
                    ->numeric(),
                Forms\Components\TextInput::make('notif_referencia_colchon')
                    ->maxLength(255),
                Forms\Components\TextInput::make('notif_nombre')
                    ->maxLength(255),
                Forms\Components\TextInput::make('notif_denomin_posicion')
                    ->maxLength(255),
                Forms\Components\TextInput::make('notif_estado_sistema')
                    ->maxLength(255),
                Forms\Components\TextInput::make('notif_autor')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('notif_fecha_creacion'),
                Forms\Components\TextInput::make('notif_hora_creacion'),
                Forms\Components\DatePicker::make('notif_fecha_liberac_real'),
                Forms\Components\TextInput::make('notif_modificado_por')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('notif_fecha_fin_notificada'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('notif_orden')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notif_fecha_puesta_dis_mat')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notif_numero_material')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notif_pedido_cliente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notif_pos_pedido_cliente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notif_cantidad_orden')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notif_cantidad_buena_notificada')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notif_referencia_colchon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notif_nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notif_denomin_posicion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notif_estado_sistema')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notif_autor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notif_fecha_creacion')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notif_hora_creacion'),
                Tables\Columns\TextColumn::make('notif_fecha_liberac_real')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notif_modificado_por')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notif_fecha_fin_notificada')
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
                ]),
                TablesExportBulkAction::make()->exports([
                    ExcelExport::make('table')->fromTable()->withFilename('Ordenes Notificadas -'. date('Y-m-d')),
                    ExcelExport::make('form')->fromForm()->withFilename('Ordenes Notificadas -'. date('Y-m-d')),
                ])
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
            'index' => Pages\ListNotificacions::route('/'),
            'create' => Pages\CreateNotificacion::route('/create'),
            'edit' => Pages\EditNotificacion::route('/{record}/edit'),
        ];
    }
}
