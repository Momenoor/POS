<?php

namespace App\Filament\Resources\BankTransactionResource\Pages;

use App\Filament\Resources\BankTransactionResource;
use App\Models\BankTransaction;
use App\Observers\JournalEntryObserver;
use App\Traits\HasJournalEntryItemRelationManager;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBankTransaction extends EditRecord
{
    use HasJournalEntryItemRelationManager;

    protected static string $resource = BankTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('Regenerate Jounral Entry')
                ->action(function (BankTransaction $record) {
                    $record->journalEntry->update(['reference_number' => $record->journalEntry->reference_number . '-deleted-' . now()->timestamp]);
                    $record->journalEntry->delete();
                    app(JournalEntryObserver::class)->generateJournalVoucher($record);
                    Notification::make()->warning()->body('Journal Entry Generated Successfully.')->send();
                })
        ];
    }

}
