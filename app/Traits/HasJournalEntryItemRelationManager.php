<?php

namespace App\Traits;

trait HasJournalEntryItemRelationManager
{
    protected function afterSave(): void
    {
        // Refresh all relation managers
        $this->dispatch('refreshJournalEntryItems');

    }
}
