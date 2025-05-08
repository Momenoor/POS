<?php

namespace App\Observers;

use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class ExpenseObserver
{
    /**
     * Handle the Expense "creating" event.
     */
    public function creating(Expense $expense): void
    {
        $this->syncDatesFromBill($expense);
    }

    /**
     * Handle the Expense "updating" event.
     */
    public function updating(Expense $expense): void
    {
        $this->syncDatesFromBill($expense);
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        // Only trigger if notes changed and journal item exists
        if ($expense->isDirty('notes') && $expense->journalItem) {
            DB::transaction(function () use ($expense) {
                $expense->journalItem->update(['memo' => $expense->notes]);
            });
        }

        // If amount changed, update related journal entries
        if ($expense->isDirty(['total', 'account_id'])) {
            $this->updateJournalAmounts($expense);
        }
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        // Soft delete the related journal item if it exists
        if ($expense->journalItem) {
            DB::transaction(function () use ($expense) {
                $expense->journalItem->delete();
            });
        }
    }

    /**
     * Sync dates from parent bill
     */
    protected function syncDatesFromBill(Expense $expense): void
    {
        // Prioritize payment date if payment exists
        if ($expense->payment?->date) {
            $expense->date = $expense->payment->date;
            $expense->due_date = $expense->payment->date;
            return;
        }

        // Fall back to bill dates if no payment exists
        if ($expense->bill) {
            $expense->date = $expense->bill->date;
            $expense->due_date = $expense->bill->due_date;
        }
    }

    /**
     * Update journal amounts when expense amount changes
     */
    protected function updateJournalAmounts(Expense $expense): void
    {
        DB::transaction(function () use ($expense) {
            if ($expense->journalItem) {
                $expense->journalItem->update([
                    'debit' => $expense->subtotal,
                    'credit' => 0,
                    'account_id'=> $expense->account_id,
                ]);
            }
        });
    }

}
