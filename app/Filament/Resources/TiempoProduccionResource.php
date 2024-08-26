<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TiempoProduccionResource\Pages;
use App\Filament\Resources\TiempoProduccionResource\RelationManagers;
use App\Models\TiempoProduccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TiempoProduccionResource extends Resource
{
    protected static ?string $model = TiempoProduccion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('referencia_colchon')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('fileteado_tapas')
                    ->numeric(),
                Forms\Components\TextInput::make('fileteado_falsos')
                    ->numeric(),
                Forms\Components\TextInput::make('maquina_rufflex')
                    ->numeric(),
                Forms\Components\TextInput::make('bordadora')
                    ->numeric(),
                Forms\Components\TextInput::make('decorado_falso')
                    ->numeric(),
                Forms\Components\TextInput::make('falso_pillow')
                    ->numeric(),
                Forms\Components\TextInput::make('encintado')
                    ->numeric(),
                Forms\Components\TextInput::make('maquina_plana')
                    ->numeric(),
                Forms\Components\TextInput::make('marquillado')
                    ->numeric(),
                Forms\Components\TextInput::make('zona_pega')
                    ->numeric(),
                Forms\Components\TextInput::make('cierre')
                    ->numeric(),
                Forms\Components\TextInput::make('empaque')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('referencia_colchon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fileteado_tapas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fileteado_falsos')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('maquina_rufflex')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bordadora')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('decorado_falso')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('falso_pillow')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('encintado')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('maquina_plana')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('marquillado')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('zona_pega')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cierre')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('empaque')
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
            'index' => Pages\ListTiempoProduccions::route('/'),
            'create' => Pages\CreateTiempoProduccion::route('/create'),
            'edit' => Pages\EditTiempoProduccion::route('/{record}/edit'),
        ];
    }
}
