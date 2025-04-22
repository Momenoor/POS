<?php

namespace App\Filament\Resources\JournalItemResource\Pages;

use App\Filament\Resources\JournalItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJournalItems extends ListRecords
{
    protected static string $resource = JournalItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
