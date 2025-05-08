<?php

namespace App\Filament\Resources;

use App\Filament\Exports\AccountExporter;
use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Accounting';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->striped()
            //->query(Account::query()->whereNull('parent_account_id')->orderBy('code'))
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->formatStateUsing(fn($state, $record) => str_repeat('— ', $record->depth) . $state)
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): array => match ($state) {
                        'asset' => Color::Green,
                        'liability' => Color::Red,
                        'equity' => Color::Blue,
                        'revenue' => Color::Purple,
                        'expense' => Color::Orange,
                    }),
                Tables\Columns\IconColumn::make('is_system_account')
                    ->boolean(),
                Tables\Columns\TextColumn::make('opening_balance')
                    ->currency()
                    ->label('Opening Balance')
                    ->sortable(),
//                    ->formatStateUsing(callback: function ($state, Tables\Columns\TextColumn $component) {
//
//                        dd(app()->make('filamentCurrency')->parseAmount($state));
//                        if ($state < 0) {
//                            $state = $state * -1;
//                            $state = app('filament-currency')->parseAmount($state);
//                            $component->color(Color::Red);
//                            return $state->format();
//                        }
//                        return $state;
//                    }),
                Tables\Columns\TextColumn::make('current_balance')
                    ->currency()
                    ->label('Current Balance')
                    ->sortable(),
//                Tables\Columns\TextColumn::make('debit_opening_balance')
//                    ->money('AED')
//                    ->label('Opening Balance')
//                    ->sortable(),
//                Tables\Columns\TextColumn::make('credit_opening_balance')
//                    ->money('AED')
//                    ->label('Opening Balance')
//                    ->sortable(),
//                Tables\Columns\TextColumn::make('debit_current_balance')
//                    ->money('AED')
//                    ->color(Color::Red)
//                    ->label('Current Balance')
//                    ->sortable(),
//                Tables\Columns\TextColumn::make('credit_current_balance')
//                    ->money('AED')
//                    ->color(Color::Green)
//                    ->label('Current Balance')
//                    ->sortable(),
//                Tables\Columns\TextColumn::make('debit_ending_balance')
//                    ->money('AED')
//                    ->label('Ending Balance')
//                    ->sortable(),
//                Tables\Columns\TextColumn::make('credit_ending_balance')
//                    ->money('AED')
//                    ->label('Ending Balance')
//                    ->sortable(),
            ])
//            ->groups([
//                Tables\Grouping\Group::make('parent_account_id')
//                    ->getTitleFromRecordUsing(fn($record) => $record->parent_account_id === null ? $record->name : null)
//                    ->getDescriptionFromRecordUsing(fn(Account $record) => $record->parent_account_id === null ? "{$record->code} - {$record->description}" : null)
//                    ->groupQueryUsing(fn(Builder $query) => $query->whereNull('parent_account_id')->orderBy('code'))
//            ])
            ->pushHeaderActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(AccountExporter::class),
                Tables\Actions\Action::make('Opening Balance Setup')
                    ->color(Color::Green)
                    ->url(route('filament.admin.pages.opening-balance-setup')),
                Tables\Actions\Action::make('Account Setup')
                    ->color(Color::Purple)
                    ->url(route('filament.admin.pages.accounts-setup')),
            ])
            ->paginated(false)
            ->defaultSort('code')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'asset' => 'Asset',
                        'liability' => 'Liability',
                        'equity' => 'Equity',
                        'revenue' => 'Revenue',
                        'expense' => 'Expense',
                    ]),
                TernaryFilter::make('leaf_only')
                    ->label('Leaf Accounts')
                    ->placeholder('All')
                    ->trueLabel('Only Leafs')
                    ->queries(
                        true: fn($query) => $query->whereDoesntHave('children'),
                        false: fn($query) => $query, // or show only parents if you want
                        blank: fn($query) => $query // no filter applied
                    ),
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
            //RelationManagers\ChildrenRelationManager::class,
            //RelationManagers\JournalEntryItemsRelationManager::class,
            //RelationManagers\ExpensesRelationManager::class,
            //RelationManagers\BankAccountRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }

    /**
     * @return array
     */
    public static function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('code')
                ->required()
                ->maxLength(20)
                ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(100),
            Forms\Components\Select::make('type')
                ->options([
                    'asset' => 'Asset',
                    'liability' => 'Liability',
                    'equity' => 'Equity',
                    'revenue' => 'Revenue',
                    'expense' => 'Expense',
                ])
                ->required(),
            Forms\Components\Select::make('parent_account_id')
                ->relationship('parent', 'name')
                ->searchable(['code', 'name', 'type'])
                ->getOptionLabelFromRecordUsing(fn($record) => '[' . $record->code . '] - ' . $record->name)
                ->preload(),
            Forms\Components\Toggle::make('is_system_account'),
            Forms\Components\TextInput::make('opening_balance')
                ->numeric()
                ->default(0),
            Forms\Components\Textarea::make('description')
                ->columnSpanFull(),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withDepth()->defaultOrder(); // Orders by lft (tree structure)
    }
}
