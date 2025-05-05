<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankAccountResource\Pages;
use App\Filament\Resources\BankAccountResource\RelationManagers;
use App\Models\BankAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use PhpParser\Builder;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Accounting';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('account.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bank_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('current_balance')
                    ->money('AED')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_primary')
                    ->beforeStateUpdated(function ($record) {
                        BankAccount::query()->update(['is_primary' => false]);
                    })
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
            RelationManagers\ReconciliationsRelationManager::class,
            RelationManagers\BankTransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBankAccounts::route('/'),
            'create' => Pages\CreateBankAccount::route('/create'),
            'edit' => Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }

    /**
     * @return array
     */
    public static function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('account_id')
                ->relationship('account', 'name')
                ->required()
                ->searchable(['code', 'name'])
                ->columnSpanFull(),
            Forms\Components\TextInput::make('bank_name')
                ->required()
                ->maxLength(100),
            Forms\Components\TextInput::make('account_number')
                ->required()
                ->maxLength(50),
            Forms\Components\TextInput::make('routing_number')
                ->maxLength(50),
            Forms\Components\TextInput::make('current_balance')
                ->numeric()
                ->default(0),
        ];
    }
}
