<?php

namespace App\Filament\Resources\BillResource\RelationManagers;

use App\Traits\InteractionWIthJournalRelationManager;
use Filament\Resources\RelationManagers\RelationManager;


class JournalEntryItemsRelationManager extends RelationManager
{
    use InteractionWIthJournalRelationManager;

    protected static string $relationship = 'JournalItems';
}
