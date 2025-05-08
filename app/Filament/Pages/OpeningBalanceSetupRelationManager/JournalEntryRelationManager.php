<?php

namespace App\Filament\Pages\OpeningBalanceSetupRelationManager;

use App\Traits\InteractionWIthJournalRelationManager;
use Filament\Resources\RelationManagers\RelationManager;

class JournalEntryRelationManager extends RelationManager
{
    use InteractionWIthJournalRelationManager;

    protected static string $relationship = 'JournalItems';

}
