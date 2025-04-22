<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankAccountResource\RelationManagers\BankTransactionsRelationManager;
use App\Filament\Resources\BankTransactionResource\Pages;
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
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('AED')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'deposit' => 'success',
                        'withdrawal' => 'danger',
                        'fee' => 'warning',
                        'interest' => 'info',
                    }),
                Tables\Columns\IconColumn::make('is_reconciled')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'deposit' => 'Deposit',
                        'withdrawal' => 'Withdrawal',
                        'fee' => 'Fee',
                        'interest' => 'Interest',
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
            //
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
            Section::make('Bank Transaction Details')
                ->columns(2)
                ->schema([
                    DatePicker::make('transaction_date')
                        ->label('Date')
                        ->required(),

                    Select::make('type')
                        ->label('Transaction Type')
                        ->options([
                            'deposit' => 'Deposit',
                            'withdrawal' => 'Withdrawal',
                            'fee' => 'Bank Fee',
                            'interest' => 'Interest',
                        ])
                        ->required(),

                    TextInput::make('reference')
                        ->label('Reference')
                        ->maxLength(50)
                        ->columnSpan(2),

                    TextInput::make('amount')
                        ->label('Amount (AED)')
                        ->numeric()
                        ->required()
                        ->columnSpan(2),
                ]),

            Section::make('Bank Account Info')
                ->columns(2)
                ->schema([
                    Select::make('bank_account_id')
                        ->label('Bank Account')
                        ->relationship('bankAccount', 'bank_name')
                        ->searchable()
                        ->required()
                        ->dehydrated(false)
                        ->default(fn($record): int => $record->bankAccount->id ?? 0)
                        ->options(BankAccount::query()->get()->pluck('bank_name', 'id')->toArray())
                        ->required(fn($livewire): bool => !$livewire instanceof BankTransactionsRelationManager)
                        ->default(fn($livewire) => $livewire instanceof BankTransactionsRelationManager
                            ? $livewire->ownerRecord?->id
                            : null
                        )
                        ->columnSpan(2)
                        ->afterStateUpdated(function (Forms\Set $set, $state) {
                            $bankAccount = BankAccount::query()->select(['account_number', 'bank_name'])->find($state);
                            if ($bankAccount) {
                                $set('account_number', $bankAccount->account_number);
                                $set('bank_name', $bankAccount->bank_name);
                            } else {
                                $set('account_number', null);
                                $set('bank_name', null);
                            }
                        })
                        ->afterStateHydrated(function (Forms\Set $set, $state) {
                            $bankAccount = BankAccount::query()->select(['account_number', 'bank_name'])->find($state);
                            if ($bankAccount) {
                                $set('account_number', $bankAccount->account_number);
                                $set('bank_name', $bankAccount->bank_name);
                            } else {
                                $set('account_number', null);
                                $set('bank_name', null);
                            }
                        })
                        ->live(onBlur: true),

                    TextInput::make('account_number')
                        ->label('Account Number')
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpan(1)
                        ->readOnly()
                        ->reactive(),

                    TextInput::make('bank_name')
                        ->label('Bank Name')
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpan(1)
                        ->readOnly()
                        ->reactive(),
                ])
                ->hidden(fn($livewire): bool => $livewire instanceof BankTransactionsRelationManager),

            Section::make()
                ->schema([
                    Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ];
    }
}
