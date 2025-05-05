<?php

namespace App\Filament\Resources;

use App\Enums\PaymentMethodEnum;
use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\BankAccount;
use App\Models\Bill;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Payment;
use App\Services\CalculationService;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Accounting';
    public static ?string $calculationOutputField = 'amount';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payable_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('payable_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('AED')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge(),
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'check' => 'Check',
                        'transfer' => 'Bank Transfer',
                        'card' => 'Credit Card',
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
            RelationManagers\JournalEntryItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    /**
     * @return array
     */
    public static function getFormSchema(): array
    {
        return array_merge(self::getSimpleFormSchema(), [
            Forms\Components\Tabs::make('Payable')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Bills')
                        ->label('Select Bills to Pay')
                        ->schema([
                            TableRepeater::make('bills')
                                ->relationship('bills')
                                ->headers([
                                    Header::make('reference_number')->label('Bill Number'),
                                    Header::make('date')->label('Bill Date'),
                                    Header::make('due_date')->label('Due Date'),
                                    Header::make('total')->label('Amount'),
                                ])
                                ->schema([
                                    Select::make('id')
                                        ->label('Select Bill')
                                        ->options(function (Get $get, $record) {
                                            return Bill::unpaid()->pluck('reference_number', 'id');
                                        })
                                        ->getOptionLabelUsing(fn($record) => $record->reference_number)
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->searchable()
                                        ->lazy()
                                        ->preload() // Better than lazy for UX
                                        ->required()
                                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                            $bill = Bill::find($state);

                                            $set('date', $bill?->date?->toDateString());
                                            $set('due_date', $bill?->due_date?->toDateString());
                                            $set('total', $bill?->total);

                                            CalculationService::calculateTransactionTotal(isNested: true)($get, $set);
                                        })
                                        ->columnSpan(1),

                                    DatePicker::make('date')
                                        ->disabled() // Better than readOnly for forms
                                        ->dehydrated()
                                        ->reactive()
                                        ->columnSpan(1),

                                    DatePicker::make('due_date')
                                        ->disabled()
                                        ->reactive()
                                        ->dehydrated()
                                        ->columnSpan(1),

                                    TextInput::make('total')
                                        ->disabled()
                                        ->dehydrated()
                                        ->reactive()
                                        ->currencyMask()
                                        ->prefix('AED')
                                        ->numeric()
                                        ->columnSpan(1),
                                ])
                                ->defaultItems(0)
                                ->columnSpan(4)
                                ->addActionLabel('Add Bill')
                                ->afterStateUpdated(function (Set $set, Get $get) {
                                    CalculationService::calculateTransactionTotal()($get, $set);
                                })
                                ->deleteAction(
                                    fn(Action $action) => $action->requiresConfirmation(),
                                )
                                ->saveRelationshipsUsing(function (Get $get, Model $record) {
                                    $billIds = collect($get('bills') ?? [])
                                        ->pluck('id')
                                        ->filter()
                                        ->unique();
                                    if ($billIds->isNotEmpty()) {
                                        Bill::whereIn('id', $billIds)->update([
                                            'payment_id' => $record->id,
                                        ]);
                                    } else {
                                        $record->bills()->update(['payment_id' => null]);;
                                    }
                                })
                                ->extraAttributes(['class' => 'border rounded-lg p-4'])
                        ]),
                    Forms\Components\Tabs\Tab::make('Expenses')
                        ->label('Add Expenses to Pay')
                        ->schema(
                            ExpenseResource::getTableSectionFormSchema(),
                        ),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function getSimpleFormSchema(): array
    {
        return [
            Forms\Components\ToggleButtons::make('payment_method')
                ->options(PaymentMethodEnum::class)
                ->icons(fn(?string $state) => PaymentMethodEnum::tryFrom($state)?->getIcon())
                ->colors(fn(?string $state) => PaymentMethodEnum::tryFrom($state)?->getColor())
                ->inlineLabel(false)
                ->default(PaymentMethodEnum::CASH->value)
                ->required()
                ->live()
                ->inline(),
            Forms\Components\DatePicker::make('payment_date')
                ->default(now()->toDateString())
                ->required(),
            Select::make('bank_account_id')
                ->label('Bank Account')
                ->options(function () {
                    return BankAccount::query()
                        ->orderBy('bank_name')
                        ->pluck('bank_name', 'id')
                        ->toArray();
                })
                ->default(fn(): int => BankAccount::query()->primary()->first()->id)
                ->searchable()
                ->reactive()
                ->hidden(fn(Get $get): bool => $get('payment_method') === PaymentMethodEnum::CASH->value)
                ->required(fn(Get $get): bool => $get('payment_method') !== PaymentMethodEnum::CASH->value)
                ->columnSpanFull()
                ->dehydrated(fn(Get $get): bool => $get('payment_method') !== PaymentMethodEnum::CASH->value),
            Forms\Components\TextInput::make('amount')
                ->default(0.00)
                ->numeric()
                ->readOnly()
                ->reactive()
                ->currencyMask()
                ->prefix('AED'),

            Forms\Components\TextInput::make('reference_number')
                ->maxLength(50),
            Forms\Components\Textarea::make('notes')
                ->columnSpanFull(),
        ];
    }

}
