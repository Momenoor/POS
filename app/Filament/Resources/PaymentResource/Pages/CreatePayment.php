<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Enums\BasicAccountsSetupEnum;
use App\Enums\PaymentMethodEnum;
use App\Filament\Resources\PaymentResource;
use App\Models\BankAccount;
use App\Models\Bill;
use App\Models\Setup;
use App\Observers\JournalEntryObserver;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (key_exists('bank_account_id', $data)) {
            $data['account_id'] = BankAccount::query()->find($data['bank_account_id'])->account_id;
            return $data;
        }

        $data['account_id'] = Setup::getAccountId(BasicAccountsSetupEnum::tryFrom(PaymentMethodEnum::tryFrom($data['payment_method'])->value));;

        return $data;
    }

//    protected function afterCreate(): void
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
