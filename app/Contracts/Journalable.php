<?php

namespace App\Contracts;

namespace App\Contracts;

interface Journalable
{
    public function getJournalItems(): array;
    public function journalEntry();
}
