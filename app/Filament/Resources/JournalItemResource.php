<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalItemResource\Pages;
use App\Filament\Resources\JournalItemResource\RelationManagers;
use App\Models\JournalItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JournalItemResource extends Resource
{
    protected static ?string $model = JournalItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Accounting';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('journal_entry_id')
                    ->relationship('journalEntry', 'reference_number')
                    ->required(),
                Forms\Components\Select::make('account_id')
                    ->relationship('account', 'name')
                    ->required(),
                Forms\Components\TextInput::make('debit')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('credit')
                    ->numeric()
                    ->default(0),
                Forms\Components\Textarea::make('memo')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('journalEntry.reference_number')
                    ->sortable(),
                Tables\Columns\TextColumn::make('account.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('debit')
                    ->money('AED')
                    ->sortable(),
                Tables\Columns\TextColumn::make('credit')
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
                Tables\Filters\SelectFilter::make('journalEntry')
                    ->relationship('journalEntry', 'reference_number'),
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
            'index' => Pages\ListJournalItems::route('/'),
            'create' => Pages\CreateJournalItem::route('/create'),
            'edit' => Pages\EditJournalItem::route('/{record}/edit'),
        ];
    }
}
