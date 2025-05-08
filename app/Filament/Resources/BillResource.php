<?php

namespace App\Filament\Resources;

use App\Enums\PaymentMethodEnum;
use App\Filament\Tables\Actions\PayBillAction;
use App\Filament\Resources\BillResouceResource\RelationManagers\PaymentRelationManager;
use App\Filament\Resources\BillResource\Pages;
use App\Filament\Resources\BillResource\RelationManagers;
use App\Models\Bill;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Accounting';
    public static ?string $calculationOutputField = 'total';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bill Information')
                    ->schema([
                            Forms\Components\TextInput::make('reference_number')
                            ->required()
                            ->serial('BILL')
                            ->maxLength(255)
                            ->columnSpan(['md' => 2, 'default' => 4])
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->default(0.00)
                            ->readOnly()
                            ->prefix('AED')
                            ->currencyMask()
                            ->columnSpan(['md' => 2, 'default' => 4])
                            ->extraInputAttributes([
                                'style' => 'color: rgb(239 68 68) !important; font-weight:bold', // red-500
                                'dark:style' => 'color: rgb(252 165 165) !important; font-weight:bold', // red-300
                            ]),

                        Forms\Components\Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->columnSpan(['md' => 2, 'default' => 4])
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm(SupplierResource::getFormSchema())
                            ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                return $action
                                    ->modalHeading('Create New Supplier')
                                    ->modalWidth('xl');
                            }),

                        Forms\Components\DatePicker::make('date')
                            ->default(now())
                            ->required()
                            ->lazy()
                            ->columnSpan(['md' => 1, 'default' => 2])
                            ->afterStateUpdated(function ($state, Set $set) {
                                $set('due_date', Carbon::parse($state)->addDays(30)->toDateString());
                            })
                            ->default(now()),

                        Forms\Components\DatePicker::make('due_date')
                            ->required()
                            ->reactive()
                            ->columnSpan(['md' => 1, 'default' => 2])
                            ->extraInputAttributes([
                                'style' => 'color: rgb(239 68 68) !important', // red-500
                                'dark:style' => 'color: rgb(252 165 165) !important', // red-300
                            ])
                            ->default(now()->addMonth()),

                        Forms\Components\Textarea::make('description')
                            ->columnSpan(4)
                            ->maxLength(500),
                    ])
                    ->columns(4),
                Forms\Components\Section::make('Expenses')
                    ->schema(ExpenseResource::getTableSectionFormSchema()),

            ])->disabled(fn(?Model $record) => $record?->isPaid());
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supplier.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment.payment_method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->money('AED')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_paid')
                    ->placeholder('All Bills')
                    ->queries(
                        true: fn($query) => $query->whereNotNull('payment_id'),
                        false: fn($query) => $query->whereNull('payment_id'),
                        blank: fn($query) => $query
                    ),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options(PaymentMethodEnum::class)
                    ->modifyQueryUsing(function (\Illuminate\Database\Eloquent\Builder $query, $state) {

                        if (blank($state['value'])) {
                            return;
                        }
                        $query->whereHas('payment', fn($q) => $q->where('payment_method', $state['value']));
                    }),

            ])
            ->actions([
                PayBillAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()->disabled(fn($record) => $record->isPaid()),
                    Tables\Actions\ViewAction::make()
                        ->modalWidth(MaxWidth::ScreenTwoExtraLarge),
                    Tables\Actions\DeleteAction::make()->disabled(fn($record) => $record->isPaid()),
                ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('Pay')
                        ->label('Pay Selected')
                        ->form(function (Form $form) {
                            return $form->schema(PaymentResource::getSimpleFormSchema());
                        })
                        ->modalHeading('Pay Bill')
                        ->modalWidth('xl')
                        ->color(Color::Orange)
                        ->icon(PaymentMethodEnum::CASH->getIcon())
                        ->action(function (Collection $records) {

                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\JournalEntryItemsRelationManager::class,
            PaymentRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBills::route('/'),
            'create' => Pages\CreateBill::route('/create'),
            'edit' => Pages\EditBill::route('/{record}/edit'),
        ];
    }

}
