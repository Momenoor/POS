<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\JournalItem;

class JournalItemObserver
{
    public function created(JournalItem $item): void
    {
        $amount = $item->debit - $item->credit;

        if ($amount == 0) {
            return;
        }

        $item->loadMissing('account.ancestors');
        $this->adjustBalance($item, $amount);
    }

    public function updated(JournalItem $item): void
    {
        if (! $this->shouldUpdateJournal($item)) {
            return;
        }

        $item->loadMissing('account.ancestors');

        $originalAccountId = $item->getOriginal('account_id');
        $originalAmount = $item->getOriginal('debit') - $item->getOriginal('credit');
        $newAmount = $item->debit - $item->credit;

        if ($originalAccountId !== $item->account_id) {
            $originalAccount = Account::with('ancestors')->find($originalAccountId);

            $this->reverseBalanceFrom($originalAccount, $originalAmount);
            $this->adjustBalance($item, $newAmount);
            return;
        }

        $this->reverseBalance($item, $originalAmount);
        $this->adjustBalance($item, $newAmount);
    }

    public function deleted(JournalItem $item): void
    {
        $amount = $item->debit - $item->credit;

        if ($amount == 0) {
            return;
        }

        $item->loadMissing('account.ancestors');
        $this->reverseBalance($item, $amount);
    }

    protected function shouldUpdateJournal(JournalItem $item): bool
    {
        return $item->wasChanged(['debit', 'credit', 'account_id', 'notes']);
    }

    protected function adjustBalance(JournalItem $item, float $amount): void
    {
        $item->account->increment('current_balance', $amount);
        $item->account->ancestors->each(
            fn($ancestor) => $ancestor->increment('current_balance', $amount)
        );
    }

    protected function reverseBalance(JournalItem $item, float $amount): void
    {
        $item->account->decrement('current_balance', $amount);
        $item->account->ancestors->each(
            fn($ancestor) => $ancestor->decrement('current_balance', $amount)
        );
    }

    protected function reverseBalanceFrom(Account $account, float $amount): void
    {
        $account->decrement('current_balance', $amount);
        $account->ancestors->each(
            fn($ancestor) => $ancestor->decrement('current_balance', $amount)
        );
    }
}
