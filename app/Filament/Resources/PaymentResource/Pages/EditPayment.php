<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\Bill;
use App\Models\Payment;
use App\Observers\JournalEntryObserver;
use App\Traits\HasJournalEntryItemRelationManager;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class EditPayment extends EditRecord
{
    use HasJournalEntryItemRelationManager;

    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('Regenerate Jounral Entry')
                ->action(function (Payment $record) {
                    $record->load(['expenses', 'bills']);
                    if ($record->journalEntry) {
                        $record->journalEntry->update(['reference_number' => $record->journalEntry->reference_number . '-deleted-' . now()->timestamp]);
                        $record->journalEntry->delete();
                    }
                    app(JournalEntryObserver::class)->generateJournalVoucher($record);
                    Notification::make()->warning()->body('Journal Entry Generated Successfully.')->send();
                })
        ];
    }

//    protected function afterSave(): void
//    {
//        JournalEntryObserver::$skipObserver = true;
//
//        $billIds = collect($this->form->getState()['bills'] ?? [])
//            ->pluck('id')
//            ->filter()
//            ->unique();
//        if ($billIds->isNotEmpty()) {
//            Log::info($billIds);;
//            Bill::whereIn('id', $billIds)->update([
//                'payment_id' => $this->record->id,
//            ]);
//        }
//
//        JournalEntryObserver::$skipObserver = false;
//        app(JournalEntryObserver::class)->updated($this->record);
//    }
}
