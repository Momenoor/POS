<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Forms\Header\Actions\PayBillAction;
use App\Filament\Resources\BillResource;
use App\Models\Bill;
use App\Observers\JournalEntryObserver;
use App\Traits\HasJournalEntryItemRelationManager;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBill extends EditRecord
{
    use HasJournalEntryItemRelationManager;

    protected static string $resource = BillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PayBillAction::make(),
            Actions\DeleteAction::make()
                ->hidden(fn($record) => $record->isPaid()),
            Actions\Action::make('Regenerate Jounral Entry')
                ->action(function (Bill $record) {
                    $record->load('expenses');
                    if ($record->journalEntry) {
                        $record->journalEntry->update(['reference_number' => $record->journalEntry->reference_number . '-deleted-' . now()->timestamp]);
                        $record->journalEntry->delete();
                    }
                    app(JournalEntryObserver::class)->generateJournalVoucher($record);
                    Notification::make()->warning()->body('Journal Entry Generated Successfully.')->send();
                })->hidden(fn($record) => $record->isPaid())
        ];
    }


}
