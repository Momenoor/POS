<?php

namespace App\Traits;

use Filament\Forms\Form;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

trait InteractionWIthJournalRelationManager
{


    protected function getListeners(): array
    {
        return [
            'refreshJournalEntryItems' => '$refresh',
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema();
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account.code')
                    ->label('Account Code'),
                TextColumn::make('account.name')
                    ->label('Account Name'),
                TextColumn::make('debit')
                    ->money('AED')
                    ->summarize(Sum::make()
                        ->label('')
                        ->money('AED')
                        ->extraAttributes(['class' => 'font-bold']))
                    ->color(Color::Red),
                TextColumn::make('credit')
                    ->money('AED')
                    ->summarize(Sum::make()
                        ->label('')
                        ->money('AED')
                        ->extraAttributes(['class' => 'font-bold']))
                    ->color(Color::Green),
                TextColumn::make('memo')
                    ->limit(50)
                    ->tooltip(fn($state) => $state),

            ]);
    }
}
