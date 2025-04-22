<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankReconciliationResource\Pages;
use App\Filament\Resources\BankReconciliationResource\RelationManagers;
use App\Models\BankReconciliation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BankReconciliationResource extends Resource
{
    protected static ?string $model = BankReconciliation::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';
    protected static ?string $navigationGroup = 'Accounting';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('bank_account_id')
                    ->relationship('bankAccount', 'account_number')
                    ->required(),
                Forms\Components\DatePicker::make('statement_date')
                    ->required(),
                Forms\Components\TextInput::make('statement_balance')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('adjusted_balance')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_completed'),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bankAccount.account_number')
                    ->sortable(),
                Tables\Columns\TextColumn::make('statement_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('statement_balance')
                    ->money('AED')
                    ->sortable(),
                Tables\Columns\TextColumn::make('adjusted_balance')
                    ->money('AED')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_completed')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('bankAccount')
                    ->relationship('bankAccount', 'account_number'),
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
            //RelationManagers\TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBankReconciliations::route('/'),
            'create' => Pages\CreateBankReconciliation::route('/create'),
            'edit' => Pages\EditBankReconciliation::route('/{record}/edit'),
        ];
    }
}
