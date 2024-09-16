<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MantenimientoProgramadoResource\Pages;
use App\Filament\Resources\MantenimientoProgramadoResource\RelationManagers;
use App\Models\MantenimientoProgramado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MantenimientoProgramadoResource extends Resource
{
    protected static ?string $model = MantenimientoProgramado::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('fecha_mantenimiento')
                    ->required()
                    ->minDate(now())
                    ->afterOrEqual(today()),
                Forms\Components\Select::make('hora_inicio')
                    ->options(MantenimientoProgramado::getOpcionesHora())
                    ->required()
                    ->reactive(),
                Forms\Components\Select::make('hora_fin')
                    ->options(function (callable $get) {
                        $horaInicio = $get('hora_inicio');
                        $todasLasHoras = MantenimientoProgramado::getOpcionesHora();
                        if (!$horaInicio) {
                            return $todasLasHoras;
                        }
                        return array_filter($todasLasHoras, function($key) use ($horaInicio) {
                            return $key > $horaInicio;
                        }, ARRAY_FILTER_USE_KEY);
                    })
                    ->required()
                    ->disabled(fn (callable $get) => !$get('hora_inicio')),
                Forms\Components\Select::make('estacion_trabajo')
                    ->label('Estación de Trabajo')
                    ->options(MantenimientoProgramado::getEstacionesTrabajo())
                    ->required()
                    ->reactive(),
                    Forms\Components\Select::make('numero_maquinas')
                    ->label('Número de Máquinas')
                    ->options(function (callable $get) {
                        $estacionTrabajo = $get('estacion_trabajo');
                        if (!$estacionTrabajo) {
                            return [];
                        }
                        return MantenimientoProgramado::getOpcionesMaquinas($estacionTrabajo);
                    })
                    ->required()
                    ->disabled(fn (callable $get) => !$get('estacion_trabajo')),
                Forms\Components\Textarea::make('descripcion')
                    ->required()
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha_mantenimiento')
                    ->date(),
                Tables\Columns\TextColumn::make('hora_inicio')
                    ->dateTime('h:i A'),
                Tables\Columns\TextColumn::make('hora_fin')
                    ->dateTime('h:i A'),
                Tables\Columns\TextColumn::make('estacion_trabajo_formateada')
                    ->label('Estación de Trabajo'),
                Tables\Columns\TextColumn::make('numero_maquinas'),
                Tables\Columns\TextColumn::make('descripcion'),
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
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMantenimientoProgramados::route('/'),
            'create' => Pages\CreateMantenimientoProgramado::route('/create'),
            'edit' => Pages\EditMantenimientoProgramado::route('/{record}/edit'),
        ];
    }
}