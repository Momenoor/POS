<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RestaurantResource\Pages;
use App\Filament\Resources\RestaurantResource\RelationManagers;
use App\Models\Restaurant;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        true => 'Active',
                        false => 'Inactive',
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
            RelationManagers\StaffRelationManager::class,
            RelationManagers\MenuCategoriesRelationManager::class,
            RelationManagers\TablesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRestaurants::route('/'),
            'create' => Pages\CreateRestaurant::route('/create'),
            'edit' => Pages\EditRestaurant::route('/{record}/edit'),
        ];
    }

    /**
     * @return array
     */
    public static function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Basic Information')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('legal_name')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('tax_id')
                        ->maxLength(50),
                    Forms\Components\Select::make('default_tax_rate_id')
                        ->relationship('taxRate', 'name'),
                ])->columns(2),

            Forms\Components\Section::make('Contact Information')
                ->schema([
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->required(),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('website')
                        ->url()
                        ->maxLength(100),
                ])->columns(3),

            Forms\Components\Section::make('Location')
                ->schema([
                    Forms\Components\Textarea::make('address')
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('city')
                        ->required()
                        ->maxLength(50),
                    Forms\Components\TextInput::make('country')
                        ->default('United Arab Emirates')
                        ->maxLength(50),
                ])->columns(2),

            Forms\Components\Section::make('Settings')
                ->schema([
                    Forms\Components\TextInput::make('timezone')
                        ->default('UTC')
                        ->maxLength(50),
                    Forms\Components\TextInput::make('currency')
                        ->default('AED')
                        ->maxLength(3),
                    Forms\Components\FileUpload::make('logo_path')
                        ->directory('restaurant-logos')
                        ->image(),
                    Forms\Components\Toggle::make('is_active')
                        ->required(),
                    TableRepeater::make('business_hours')
                        ->minItems(1)
                        ->headers([
                            Header::make('Day'),
                            Header::make('Start Time'),
                            Header::make('End Time'),
                        ])
                        ->schema([
                            Forms\Components\Select::make('day')
                                ->options(Carbon::getDays())->required(),
                            Forms\Components\TimePicker::make('start_time')->required()->default(Carbon::tim)->format('H:i'),
                            Forms\Components\TimePicker::make('end_time')->required()->format('H:i'),
                        ])
                        ->columnSpanFull(),
                ])->columns(3),
        ];
    }
}
