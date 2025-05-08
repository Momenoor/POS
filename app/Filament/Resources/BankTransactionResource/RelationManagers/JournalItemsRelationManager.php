<?php

namespace App\Filament\Resources\BankTransactionResource\RelationManagers;

use App\Traits\InteractionWIthJournalRelationManager;
use Filament\Resources\RelationManagers\RelationManager;


class JournalItemsRelationManager extends RelationManager
{
    use InteractionWIthJournalRelationManager;

    protected static string $relationship = 'JournalItems';
}
