<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use App\Services\CalculationService;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Livewire;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Accounting';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('account_id')
                    ->relationship('account', 'name', fn(Builder $query) => $query->expenses()->orderBy('code'))
                    ->getOptionLabelFromRecordUsing(fn($record) => '[' . $record->code . '] - ' . $record->name)
                    ->searchable(['code', 'name'])
                    ->required(),
                Forms\Components\Select::make('supplier_id')
                    ->relationship('supplier', 'name'),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_paid')
                    ->required(),
                Forms\Components\Select::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'check' => 'Check',
                        'transfer' => 'Bank Transfer',
                        'card' => 'Credit Card',
                    ]),
                Forms\Components\TextInput::make('reference')
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('account.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bill.supplier.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->money('AED')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_paid')
                    ->boolean(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_paid')
                    ->options([
                        true => 'Paid',
                        false => 'Unpaid',
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
            //RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }

    public static function getTableSectionFormSchema(): array
    {
        return
            [
                TableRepeater::make('expenses')
                    ->headers([
                        Header::make('Account')->width('27%'),
                        Header::make('Qty')->width('10%'),
                        Header::make('Unit Price')->width('16%'),
                        Header::make('Total')->width('16%'),
                        Header::make('Notes')->width('32%'),
                    ])
                    ->schema([
                        Forms\Components\Select::make('account_id')
                            ->relationship('account', 'name', fn(Builder $query) => $query->expenses())
                            ->getOptionLabelFromRecordUsing(fn($record) => "[{$record->code}] {$record->name}")
                            ->searchable(['name', 'code'])
                            ->preload()
                            ->required()
                            ->columnSpan(['md' => 2, 'default' => 4])
                            ->createOptionForm(AccountResource::getFormSchema())
                            ->createOptionAction(function (Action $action) {
                                return $action
                                    ->modalHeading('Create New Account')
                                    ->modalWidth('xl');
                            }),

                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->lazy()
                            ->columnSpan(['md' => 1, 'default' => 2])
                            ->afterStateUpdated(self::calculateExpensesTotal()),

                        Forms\Components\TextInput::make('unit_cost')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->lazy()
                            ->minValue(0)
                            ->prefix('AED')
                            ->columnSpan(['md' => 1, 'default' => 2])
                            ->currencyMask()
                            ->afterStateUpdated(self::calculateExpensesTotal()),

                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->reactive()
                            ->prefix('AED')
                            ->columnSpan(['md' => 1, 'default' => 2])
                            ->currencyMask()
                            ->extraInputAttributes(['class' => 'font-bold']),

                        Forms\Components\TextInput::make('notes')
                            ->columnSpan(['md' => 4, 'default' => 4])
                            ->maxLength(255),
                    ])
                    ->relationship('expenses')
                    ->columnSpan(4)
                    ->defaultItems(0)
                    ->addActionLabel('Add Expense')
                    ->deleteAction(
                        fn(Action $action) => $action->requiresConfirmation(),
                    )
                    ->afterStateUpdated(function ($livewire, Set $set, Get $get) {
                        CalculationService::calculateTransactionTotal(config: [
                            'output_field' => $livewire->getResource()::$calculationOutputField ?: 'total',
                        ])($get, $set);
                    })
                    ->extraAttributes(['class' => 'border rounded-lg p-4'])
            ];
    }

    public static function calculateExpensesTotal(): \Closure
    {
        return function (Get $get, Set $set) {
            $unitCost = floatval($get('unit_cost'));
            $quantity = floatval($get('quantity'));
            $set('subtotal', $quantity * $unitCost);
        };
    }
}
