<?php

namespace App\Observers;

use App\Models\BankTransaction;
use App\Services\CalculationService;

class BankTransactionObserver
{
    /**
     * Handle the BankTransaction "created" event.
     */
    public function created(BankTransaction $bankTransaction): void
    {
        CalculationService::updateBanksBalance();
    }

    /**
     * Handle the BankTransaction "updated" event.
     */
    public function updated(BankTransaction $bankTransaction): void
    {
        CalculationService::updateBanksBalance();
    }

    /**
     * Handle the BankTransaction "deleted" event.
     */
    public function deleted(BankTransaction $bankTransaction): void
    {
        CalculationService::updateBanksBalance();
    }

    /**
     * Handle the BankTransaction "restored" event.
     */
    public function restored(BankTransaction $bankTransaction): void
    {
        CalculationService::updateBanksBalance();
    }

    /**
     * Handle the BankTransaction "force deleted" event.
     */
    public function forceDeleted(BankTransaction $bankTransaction): void
    {
        CalculationService::updateBanksBalance();
    }
}
