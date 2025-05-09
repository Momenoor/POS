<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuCategoryResource\RelationManagers\MenuItemsRelationManager;
use App\Filament\Resources\MenuItemResource\Pages;
use App\Filament\Resources\MenuItemResource\RelationManagers;
use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-bars-4';
    protected static ?string $navigationGroup = 'Menu Management';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('AED')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_available')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
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
            //RelationManagers\OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }

    /**
     * @return array
     */
    public static function getFormSchema(): array
    {
        return [
            Section::make('Item Details')
                ->schema([
                    FileUpload::make('image')
                        ->directory('menu-items')
                        ->circleCropper()
                        ->image()
                        ->columnSpanFull(),

                    TextInput::make('name')
                        ->required()
                        ->maxLength(100),

                    Textarea::make('description')
                        ->maxLength(255),

                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->required()
                        ->default(function ($livewire) {
                            ($livewire instanceof MenuItemsRelationManager)
                                ? $livewire->getOwnerRecord()->id
                                : null;
                        })
                        ->hidden(fn($livewire) => $livewire instanceof MenuItemsRelationManager),
                ])
                ->columns(2),

            Section::make('Pricing')
                ->schema([
                    TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->prefix('AED'),

                    TextInput::make('cost')
                        ->numeric()
                        ->prefix('AED'),
                ])
                ->columns(2),

            Section::make('Tax & Availability')
                ->schema([
                    Toggle::make('is_taxable')
                        ->required()
                        ->lazy(),


                    Toggle::make('is_available')
                        ->required()
                        ->default(true),

                    Select::make('tax_rate_id')
                        ->relationship('taxRate', 'name')
                        ->reactive()
                        ->hidden(fn(Forms\Get $get): bool => !$get('is_taxable')),
                ])
                ->columns(2),
        ];
    }
}
