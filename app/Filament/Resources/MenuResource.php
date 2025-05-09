<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Filament\Resources\MenuResource\RelationManagers\MenuCategoryItemRelationManager;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Set;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-2';
    protected static ?string $navigationGroup = 'Menu Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Menu Details')->schema([
                    TextInput::make('name')
                        ->required(),
                    Textarea::make('description'),
                    Toggle::make('is_active'),
                    CheckboxList::make('menu_categories')
                        ->relationship('menuCategories', 'name', fn($query) => $query->orderBy('id'))
                        ->required()
                        ->columns(4)
                        ->saveRelationshipsUsing(fn() => false)
                        ->afterStateUpdated(function ($state, $component, Set $set) {
                            // Step 2: Get all related items
                            $menuItems = \App\Models\MenuItem::whereIn('category_id', $state)->get();

                            // Step 3: Transform for repeater
                            $items = $menuItems->map(function ($item) {
                                return [
                                    'item_id' => $item->id,
                                    'name' => $item->name,
                                    'menu_cost' => $item->cost,
                                    'menu_price' => $item->price,
                                    'menu_is_available' => $item->is_available,
                                    'menu_is_taxable' => $item->is_taxable,
                                    'menu_tax_rate_id' => $item->tax_rate_id,
                                    'category_id' => $item->category_id,
                                ];
                            })->toArray();

                            // Step 4: Mount into repeater
                            $set('category_items', $items);
                        })
                        ->live(),
                    TableRepeater::make('category_items')
                        ->headers([
                            Header::make('name'),
                            Header::make('cost')
                                ->width('15%'),
                            Header::make('price')
                                ->width('15%'),
                            Header::make('is available'),
                            Header::make('is taxable'),
                            Header::make('tax rate'),
                        ])
                        ->schema([
                            Hidden::make('item_id')
                            ,
                            Hidden::make('category_id')
                            ,
                            TextInput::make('name')
                                ->disabled(),
                            TextInput::make('menu_cost')
                                ->currencyMask()
                                ->prefix('AED'),
                            TextInput::make('menu_price')
                                ->currencyMask()
                                ->prefix('AED'),
                            Toggle::make('menu_is_available'),
                            Toggle::make('menu_is_taxable')
                                ->lazy(),
                            Select::make('menu_tax_rate_id')
                                ->disabled(fn(Get $get) => !$get('is_taxable'))
                                ->options(function () {
                                    static $taxRates = null;
                                    if ($taxRates === null) {
                                        $taxRates = \App\Models\TaxRate::all()->pluck('name', 'id');
                                    }
                                    return $taxRates;
                                })
                                ->reactive(),
                        ])
                        ->default([])
                        ->reactive()
                        ->relationship('categoryItems')
                        ->saveRelationshipsUsing(function ($record, $state) {
                            $record->categoryItems()->sync(collect($state)->mapWithKeys(function ($item) {
                                return [
                                    $item['item_id'] => collect($item)->only([
                                        'category_id', 'menu_cost', 'menu_price', 'item_id',
                                        'menu_is_available', 'menu_is_taxable', 'menu_tax_rate_id'
                                    ])
                                ];
                            })->toArray());
                        })
                        ->reorderable(false)
                        ->addable(false)
                        ->columnSpanFull(),
                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->searchable(),
                Tables\Columns\TextColumn::make('menuCategories.name')
                    ->label('Categories')
                    ->getStateUsing(function ($record) {
                        return $record->categoryItems
                            ->loadMissing('category')
                            ->pluck('category.name')
                            ->unique();
                    })
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('category_items_count')
                    ->counts('categoryItems')
                    ->label('Items'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->searchPlaceholder('Menus names only...')
            ->searchable();
    }

    public static function getRelations(): array
    {
        return [
            MenuCategoryItemRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
