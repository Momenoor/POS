<?php

namespace App\Filament\Resources\MenuResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Builder;

class MenuCategoryItemRelationManager extends RelationManager
{
    protected static string $relationship = 'categoryItems'; // This is the method in Menu.php

    protected static ?string $title = 'Menu Items';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('category_id')
                ->relationship('category', 'name')
                ->lazy()
                ->required(),

            Forms\Components\Select::make('item_id')
                ->label('Item')
                ->relationship('category.menuItems', 'name', modifyQueryUsing: function (Builder $query, Forms\Get $get) {
                    $query->where('category_id', $get('category_id'));
                })
                ->reactive()
                ->preload()
                ->required(),

            Forms\Components\TextInput::make('menu_price')
                ->numeric()
                ->prefix('AED')
                ->required(),

            Forms\Components\TextInput::make('menu_cost')
                ->numeric()
                ->prefix('AED'),

            Forms\Components\Toggle::make('menu_is_available')
                ->required(),

            Forms\Components\Toggle::make('menu_is_taxable')
                ->required(),

            Forms\Components\Select::make('menu_tax_rate_id')
                ->relationship('taxRate', 'name')
                ->hidden(fn(Forms\Get $get) => !$get('is_taxable')),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('menu_price')->money('AED'),
                Tables\Columns\TextColumn::make('menu_cost')->money('AED'),
                Tables\Columns\IconColumn::make('menu_is_available')->label('Item Is Available')->boolean(),
                Tables\Columns\IconColumn::make('menu_is_taxable')->label('Item Is Taxable')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name'),
            ]);
    }

    public static function getModelLabel(): string
    {
        return 'Menu Item';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Menu Items';
    }
}
