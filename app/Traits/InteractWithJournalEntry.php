<?php

namespace App\Traits;

use App\Models\JournalEntry;
use App\Models\JournalItem;

Trait InteractWithJournalEntry
{
    public function journalEntry()
    {
        return $this->morphOne(JournalEntry::class, 'referenceable');
    }

    public function journalItems(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(JournalItem::class, JournalEntry::class, 'referenceable_id', 'journal_entry_id', 'id', 'id');
    }
}
