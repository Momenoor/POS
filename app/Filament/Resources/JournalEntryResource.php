<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalEntryResource\Pages;
use App\Filament\Resources\JournalEntryResource\RelationManagers;
use App\Models\JournalEntry;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JournalEntryResource extends Resource
{
    protected static ?string $model = JournalEntry::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Accounting';

    public static function form(Form $form): Form
    {
        return $form
            ->disabled(fn(?Model $record): bool => isset($record?->referenceable))
            ->columns(2)
            ->schema([
                Forms\Components\Section::make('Journal Entry Details')
                    ->schema([
                        Forms\Components\DatePicker::make('entry_date')
                            ->required(),
                        Forms\Components\TextInput::make('reference_number')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('memo')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Journal Items')
                    ->schema([TableRepeater::make('journalItems')
                        ->label('')
                        ->headers([
                            Header::make('Account'),
                            Header::make('Debit'),
                            Header::make('Credit'),
                            Header::make('Memo'),
                        ])
                        ->schema([
                            Forms\Components\Select::make('account_id')
                                ->relationship('account', 'name')
                                ->required(),
                            Forms\Components\TextInput::make('debit')
                                ->default(0)
                                ->numeric()
                                ->rules(['numeric', 'min:0'])
                                ->currencyMask(
                                    thousandSeparator: ',',
                                    decimalSeparator: '.',
                                    precision: 2,
                                )
                                ->prefix('AED')
                                ->afterStateHydrated(function ($state, Set $set) {
                                    $set('debit', $state);
                                })
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    if ($state > 0) {
                                        $set('credit', 0);
                                    }

                                })
                                ->lazy(),

                            Forms\Components\TextInput::make('credit')
                                ->default(0)
                                ->numeric()
                                ->rules(['numeric', 'min:0'])
                                ->currencyMask(
                                    thousandSeparator: ',',
                                    decimalSeparator: '.',
                                    precision: 2,
                                )
                                ->prefix('AED')
                                ->afterStateHydrated(function ($state, Set $set) {
                                    $set('credit', $state);
                                })
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    if ($state > 0) {
                                        $set('debit', 0);
                                    }

                                })
                                ->lazy(),
                            Forms\Components\TextInput::make('memo'),
                        ])
                        ->afterStateUpdated(function ($state, Set $set, Get $get) {

                            $totalDebit = collect($get('journalItems'))->sum(function ($item) {
                                return floatval($item['debit']);
                            });
                            $totalCredit = collect($get('journalItems'))->sum(function ($item) {
                                return floatval($item['credit']);
                            });

                            $set('balance', ($totalDebit - $totalCredit));
                        })
                        ->relationship('journalItems')
                        ->minItems(2)
                        ->defaultItems(2)
                        ->mutateRelationshipDataBeforeCreateUsing(function (JournalEntry $record, array $data) {
                            $data['referenceable_type'] = get_class($record);
                            $data['referenceable_id'] = $record->id;
                            return $data;
                        })
                        ->deleteAction(
                            fn(Action $action) => $action->requiresConfirmation(),
                        )
                        ->columnSpanFull(),
                        Forms\Components\TextInput::make('balance')
                            ->prefix('AED')
                            ->currencyMask()
                            ->disabled()
                            ->reactive()
                            ->default(0)
                            ->rules(['numeric', 'min:0', 'max:0'])
                            ->validationMessages(['min' => ':attribute must be equal = zero ', 'max' => ':attribute must be equal = zero ']),
                    ])
            ]);
    }

    public
    static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entry_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_debit')->money('AED'),
                Tables\Columns\TextColumn::make('total_credit')->money('AED'),
                Tables\Columns\TextColumn::make('referenceable_type')->formatStateUsing(fn($state) => Str::of($state)->afterLast('\\')),
                Tables\Columns\TextColumn::make('memo')->limit(30)->tooltip(fn($record): string => $record->memo ?? 'No Memo'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public
    static function getRelations(): array
    {
        return [

        ];
    }

    public
    static function getPages(): array
    {
        return [
            'index' => Pages\ListJournalEntries::route('/'),
            'create' => Pages\CreateJournalEntry::route('/create'),
            'edit' => Pages\EditJournalEntry::route('/{record}/edit'),
        ];
    }
}
