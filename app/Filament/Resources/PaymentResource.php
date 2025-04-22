<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
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
            //
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
        return [
            Forms\Components\Select::make('payable_type')
                ->options([
                    'App\Models\Order' => 'Order',
                    'App\Models\Expense' => 'Expense',
                ])
                ->live(onBlur: true)
                ->required(),
            Forms\Components\Select::make('payable_id')
                ->required()
                ->reactive()
                ->options(function (Get $get) {
                    return match ($get('payable_type')) {

                        'App\Models\Order' => Order::query()
                            ->unpaid()
                            ->active()
                            ->get()
                            ->pluck('total', 'id')
                            ->map(fn($total, $id) => "Order #$id - AED $total")
                            ->toArray(),

                        'App\Models\Expense' => Expense::query()
                            ->unpaid()
                            ->get()
                            ->pluck('amount', 'id')
                            ->map(fn($amount, $id) => "Expense #$id - AED $amount")
                            ->toArray(),

                        default => [],
                    };
                }),
            Forms\Components\TextInput::make('amount')
                ->required()
                ->suffix('AED')
                ->numeric(),
            Forms\Components\DatePicker::make('payment_date')
                ->required(),
            Forms\Components\ToggleButtons::make('payment_method')
                ->options([
                    'cash' => 'Cash',
                    'check' => 'Check',
                    'transfer' => 'Bank Transfer',
                    'card' => 'Credit Card',
                ])
                ->icons([
                    'cash' => 'heroicon-o-banknotes',
                    'check' => 'heroicon-o-document-check',
                    'transfer' => 'heroicon-o-arrow-path-rounded-square',
                    'card' => 'heroicon-o-credit-card',
                ])
                ->colors([
                    'cash' => Color::Green,
                    'check' => Color::Blue,
                    'transfer' => Color::Purple,
                    'card' => Color::Orange,
                ])
                ->inlineLabel(false)
                ->default('cash')
                ->required()
                ->inline(),
            Forms\Components\TextInput::make('reference')
                ->maxLength(50),
            Forms\Components\Textarea::make('notes')
                ->columnSpanFull(),
        ];
    }
}
