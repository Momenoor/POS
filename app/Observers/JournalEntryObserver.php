<?php

namespace App\Observers;

use App\Enums\BasicAccountsSetupEnum;
use App\Models\JournalEntry;
use App\Models\Setup;
use App\Services\CalculationService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JournalEntryObserver implements ShouldHandleEventsAfterCommit
{

    public static bool $skipObserver = false;

    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $this->generateJournalVoucher($model);
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        if ($this->shouldRegenerateJournal($model)) {
            $this->generateJournalVoucher($model, true);
        }
    }

    public function deleted(Model $model): void
    {
        DB::transaction(function () use ($model) {
            if ($journalEntry = $model->journalEntry) {
                $journalEntry->journalItems()->delete();
                $journalEntry->delete();
            }
        });
    }

    /**
     * Determine if journal should be regenerated on update
     */
    protected function shouldRegenerateJournal(Model $model): bool
    {
        return $model->wasChanged();
    }


    /**
     * Generate journal voucher for the model
     */

    public function generateJournalVoucher(Model $model, bool $isUpdate = false): void
    {
        // Skip if the model doesn't implement the Journalable interface or doesn't have journalItems
        if (!method_exists($model, 'getJournalItems') || !method_exists($model, 'journalEntry')) {
            return;
        }

        DB::transaction(function () use ($model, $isUpdate) {
            $journalEntryData = $this->prepareJournalEntryData($model);
            $journalItems = $model->getJournalItems();

            if ($isUpdate) {
                $this->handleUpdate($model, $journalEntryData, $journalItems);
            } else {
                $this->handleCreate($model, $journalEntryData, $journalItems);
            }
        });
    }

    /**
     * Prepare journal entry data from the model
     */
    protected function prepareJournalEntryData(Model $model): array
    {
        return [
            'entry_date' => $model->date ?? now(),
            'reference_number' => 'JV-' . ($model->reference_number ?? $model->id),
            'memo' => 'Auto Generated JV for ' . class_basename($model) .
                ' #' . ($model->reference_number ?? $model->id),
            'amount' => $model->total ?? 0,
        ];
    }

    /**
     * Handle creation of new journal entry
     */
    protected function handleCreate(Model $model, array $entryData, array $items): void
    {
        $journalEntry = $model->journalEntry()->create($entryData);
        $journalEntry->journalItems()->createMany($items);
    }

    /**
     * Handle update of existing journal entry
     */
    protected function handleUpdate(Model $model, array $entryData, array $items): void
    {
        if ($model->journalEntry) {
            $model->journalEntry->update($entryData);
            $model->journalEntry->journalItems()->delete();
            $model->journalEntry->journalItems()->createMany($items);

        } else {
            $this->handleCreate($model, $entryData, $items);
        }
    }
}
