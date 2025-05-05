<?php

namespace App\Filament\Resources;

use App\Enums\BankTransactionTypeEnum;
use App\Filament\Resources\BankAccountResource\RelationManagers\BankTransactionsRelationManager;
use App\Filament\Resources\BankTransactionResource\Pages;
use App\Filament\Resources\BankTransactionResource\RelationManagers\JournalItemsRelationManager;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;

class BankTransactionResource extends Resource
{
    protected static ?string $model = BankTransaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
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
                Tables\Columns\TextColumn::make('bankAccount.account_number')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(fn($state) => $state)
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('AED')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn($state) => $state->getColor()),
                Tables\Columns\IconColumn::make('is_reconciled')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(BankTransactionTypeEnum::class),
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
            JournalItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBankTransactions::route('/'),
            'create' => Pages\CreateBankTransaction::route('/create'),
            'edit' => Pages\EditBankTransaction::route('/{record}/edit'),
        ];
    }

    /**
     * @return array
     */
    public static function getFormSchema(): array
    {
        return [
            Section::make('Transaction Details')
                ->columns(2)
                ->schema([
                    DatePicker::make('date')
                        ->label('Transaction Date')
                        ->required()
                        ->default(now())
                        ->maxDate(now())
                        ->columnSpan(1),

                    Select::make('type')
                        ->label('Transaction Type')
                        ->options(BankTransactionTypeEnum::class)
                        ->required()
                        ->live()
                        ->columnSpan(1),

                    TextInput::make('reference_number')
                        ->label('Reference Number')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50)
                        ->columnSpan(2)
                        ->serial(prefix: 'BNK', length: 6),

                    TextInput::make('amount')
                        ->label('Amount (AED)')
                        ->required()
                        ->numeric()
                        ->minValue(0.01)
                        ->currencyMask()
                        ->columnSpan(2),
                ]),

            Section::make('Bank Account Information')
                ->columns(2)
                ->schema([
                    Select::make('bank_account_id')
                        ->label('Bank Account')
                        ->relationship('bankAccount', 'bank_name')
                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record->bank_name} - {$record->account_number}")
                        ->searchable(['bank_name', 'account_number'])
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            $bankAccount = \App\Models\BankAccount::find($state);
                            $set('account_number', $bankAccount?->account_number);
                            $set('bank_name', $bankAccount?->bank_name);
                            $set('current_balance', $bankAccount?->current_balance);
                        })
                        ->afterStateHydrated(function ($state, Forms\Set $set) {
                            $bankAccount = \App\Models\BankAccount::find($state);
                            $set('account_number', $bankAccount?->account_number);
                            $set('bank_name', $bankAccount?->bank_name);
                            $set('current_balance', $bankAccount?->current_balance);
                        })
                        ->hint(function ($component) {
                            $state = $component->getState();
                            if (!$state) return null;
                            $balance = \App\Models\BankAccount::find($state)?->current_balance;
                            return $balance ? 'Current Balance: AED ' . number_format($balance, 2) : null;
                        })
                        ->columnSpan(2),

                    TextInput::make('account_number')
                        ->label('Account Number')
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpan(1),

                    TextInput::make('bank_name')
                        ->label('Bank Name')
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpan(1),

                    TextInput::make('current_balance')
                        ->label('Current Balance')
                        ->disabled()
                        ->dehydrated(false)
                        ->prefix('AED')
                        ->numeric()
                        ->columnSpan(2),
                ])
                ->hidden(fn($livewire): bool => $livewire instanceof BankTransactionsRelationManager),

            Section::make('Counterparty Information')
                ->schema([
                    Select::make('account_id')
                        ->label(fn(Forms\Get $get): string => $get('type') === BankTransactionTypeEnum::DEPOSIT->value
                            ? 'Source Account'
                            : 'Destination Account')
                        ->relationship('account', 'name')
                        ->getOptionLabelFromRecordUsing(fn($record) => "[$record->code] - $record->name")
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpan(2),
                ])
                ->hidden(fn(Forms\Get $get): bool => !$get('type')),

            Section::make('Additional Information')
                ->schema([
                    Textarea::make('description')
                        ->label('Description/Notes')
                        ->maxLength(500)
                        ->columnSpanFull(),
                ]),
        ];
    }
}
