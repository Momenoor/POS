<?php

namespace App\Observers;

use App\Models\JournalEntry;
use Illuminate\Support\Facades\Request;

class JournalObserver
{


    /**
     * Handle the JournalEntry "created" event.
     */
    public function created(JournalEntry $journalEntry): void
    {
        //
    }

    /**
     * Handle the JournalEntry "updated" event.
     */
    public function updated(JournalEntry $journalEntry): void
    {
        //
    }

    /**
     * Handle the JournalEntry "deleted" event.
     */
    public function deleted(JournalEntry $journalEntry): void
    {
        if ($journalEntry->journalItems) {
            $journalEntry->journalItems()->delete();
        }
    }

    /**
     * Handle the JournalEntry "restored" event.
     */
    public function restored(JournalEntry $journalEntry): void
    {
        //
    }

    /**
     * Handle the JournalEntry "force deleted" event.
     */
    public function forceDeleted(JournalEntry $journalEntry): void
    {
        //
    }
}
