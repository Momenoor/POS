<?php

namespace App\Filament\Resources\MenuCategoryResource\RelationManagers;

use App\Filament\Resources\MenuItemResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;


class MenuItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'MenuItems';

    public function form(Form $form): Form
    {
        return $form
            ->schema(MenuItemResource::getFormSchema());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular(),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('price')
                    ->color(Color::Green)
                    ->money(),
                Tables\Columns\TextColumn::make('cost')
                    ->color(Color::Red)
                    ->money(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
