<?php

namespace App\Filament\Resources;

use App\Filament\Exports\AccountExporter;
use App\Filament\Resources\AccountResource\Pages;
use App\Filament\Resources\AccountResource\RelationManagers;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
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
            ->schema([
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
                    ->preload(),
                Forms\Components\Toggle::make('is_system_account'),
                Forms\Components\TextInput::make('opening_balance')
                    ->numeric()
                    ->default(0),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            //->query(Account::query()->whereNull('parent_account_id')->orderBy('code'))
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->listWithLineBreaks()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->formatStateUsing(fn($state, $record) => str_repeat('— ', $record->depth) . $state)
                    ->searchable()
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->listWithLineBreaks()
                    ->color(fn(string $state): array => match ($state) {
                        'asset' => Color::Green,
                        'liability' => Color::Red,
                        'equity' => Color::Blue,
                        'revenue' => Color::Purple,
                        'expense' => Color::Orange,
                    }),
                Tables\Columns\IconColumn::make('is_system_account')
                    ->boolean()
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('opening_balance')
                    ->money('AED')
                    ->sortable()
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('current_balance')
                    ->money('AED')
                    ->sortable()
                    ->listWithLineBreaks(),
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
                Tables\Actions\Action::make('setup')
                    ->label('Accounts Setup')
                    ->url(route('filament.admin.resources.accounts.setup'))
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
            //RelationManagers\JournalItemsRelationManager::class,
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
            'setup' => Pages\AccountsSetup::route('/setup'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withSum('journalItems as total_debit', 'debit')
            ->withSum('journalItems as total_credit', 'credit')
            ->with(['parent', 'parent.parent']);
    }
}
