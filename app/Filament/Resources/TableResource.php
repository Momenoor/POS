<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RestaurantResource\RelationManagers\MenuCategoriesRelationManager;
use App\Filament\Resources\RestaurantResource\RelationManagers\TablesRelationManager;
use App\Filament\Resources\TableResource\Pages;
use App\Filament\Resources\TableResource\RelationManagers;
use App\Models\Table;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table as FilamentTable;

class TableResource extends Resource
{
    protected static ?string $model = Table::class;
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationGroup = 'POS Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getFormSchema());
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'available' => 'success',
                        'occupied' => 'danger',
                        'reserved' => 'warning',
                        'out_of_service' => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('restaurant')
                    ->relationship('restaurant', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'occupied' => 'Occupied',
                        'reserved' => 'Reserved',
                        'out_of_service' => 'Out of Service',
                    ]),
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
            //RelationManagers\OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTables::route('/'),
            'create' => Pages\CreateTable::route('/create'),
            'edit' => Pages\EditTable::route('/{record}/edit'),
        ];
    }

    /**
     * @return array
     */
    public static function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('restaurant_id')
                ->relationship('restaurant', 'name')
                ->required(fn($livewire): bool => $livewire instanceof TablesRelationManager)
                ->hidden(fn($livewire): bool => $livewire instanceof TablesRelationManager)
                ->default(fn($livewire) => $livewire instanceof TablesRelationManager
                    ? $livewire->ownerRecord?->id
                    : null
                ),
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(50),
            Forms\Components\TextInput::make('capacity')
                ->required()
                ->numeric(),
            Forms\Components\Select::make('status')
                ->options([
                    'available' => 'Available',
                    'occupied' => 'Occupied',
                    'reserved' => 'Reserved',
                    'out_of_service' => 'Out of Service',
                ])
                ->default('available')
                ->required(),
            Forms\Components\Textarea::make('notes')
                ->columnSpanFull(),
        ];
    }
}
