<?php

namespace App\Filament\Resources;

use App\Enums\BasicAccountsSetupEnum;
use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Setup;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Inventory Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_person')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
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
            //RelationManagers\InventoryItemsRelationManager::class,
            //RelationManagers\InventoryTransactionsRelationManager::class,
            //RelationManagers\ExpensesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }

    /**
     * @return array
     */
    public static function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Supplier Information')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->columnSpanFull()
                        ->maxLength(100),

                    Forms\Components\TextInput::make('tax_id')
                        ->maxLength(50)
                        ->columnSpan(1),

                    Forms\Components\Select::make('account_id')
                        ->relationship('account', 'name')
                        ->required()
                        ->columnSpan(1)
                        ->default(
                            Setup::where('name', BasicAccountsSetupEnum::ACCOUNTS_PAYABLE)
                                ->first()
                                ->account_id
                        ),
                ])
                ->columns(2),

            Forms\Components\Section::make('Contact Details')
                ->schema([
                    Forms\Components\Textarea::make('address')
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('contact_person')
                        ->maxLength(100)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->columnSpan(1)
                        ->required(),

                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->columnSpan(1)
                        ->maxLength(100),
                ])
                ->columns(2),
        ];
    }
}
