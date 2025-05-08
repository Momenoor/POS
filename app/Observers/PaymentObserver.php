<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\CalculationService;

class PaymentObserver
{

    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        CalculationService::updateBanksBalance();
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        CalculationService::updateBanksBalance();
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        CalculationService::updateBanksBalance();
        $payment->bills()->update(['payment_id' => null]);
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        CalculationService::updateBanksBalance();
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        CalculationService::updateBanksBalance();
        $payment->bills()->update(['payment_id' => null]);
        $payment->expenses()->update(['payment_id' => null]);
    }
}
