<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CapacidadResource\Pages;
use App\Filament\Resources\CapacidadResource\RelationManagers;
use App\Models\Capacidad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CapacidadResource extends Resource
{
    protected static ?string $model = Capacidad::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('estacion_trabajo')
                ->label('Estación de Trabajo')
    ->options(function () {
        // Obtener todas las opciones posibles
        $allOptions = [
            'fileteado_tapas' => 'Fileteado Tapas',
            'fileteado_falsos' => 'Fileteado Falsos',
            'maquina_rufflex' => 'Máquina Rufflex',
            'bordadora' => 'Bordadora',
            'decorado_falso' => 'Decorado Falso',
            'falso_pillow' => 'Falso Pillow',
            'encintado' => 'Encintado',
            'maquina_plana' => 'Máquina Plana',
            'marquillado' => 'Marquillado',
            'zona_pega' => 'Zona Pega',
            'cierre' => 'Cierre',
            'empaque' => 'Empaque',
        ];

        // Obtener las opciones ya seleccionadas
        $usedOptions = Capacidad::pluck('estacion_trabajo')->toArray();

        // Filtrar las opciones disponibles
        return array_diff_assoc($allOptions, array_flip($usedOptions));
    })
    ->required()
    ->placeholder('Selecciona una estación de trabajo')
    ->default('Selecciona una estación de trabajo'), // Si deseas un valor predeterminado
                Forms\Components\TextInput::make('numero_maquinas')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('tiempo_jornada')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('estacion_trabajo')
                    ->label('Estación de Trabajo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('numero_maquinas')
                    ->label('Numero de maquinas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tiempo_jornada')
                    ->label('Jornada laboral (minutos)')
                    ->numeric()
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCapacidads::route('/'),
            'create' => Pages\CreateCapacidad::route('/create'),
            'edit' => Pages\EditCapacidad::route('/{record}/edit'),
        ];
    }
}
