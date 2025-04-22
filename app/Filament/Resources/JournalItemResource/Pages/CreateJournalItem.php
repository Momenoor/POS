<?php

namespace App\Filament\Resources\JournalItemResource\Pages;

use App\Filament\Resources\JournalItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJournalItem extends CreateRecord
{
    protected static string $resource = JournalItemResource::class;
}
