<?php

namespace App\Filament\Actions;

use App\Enums\BasicAccountsSetupEnum;
use App\Enums\PaymentMethodEnum;
use App\Filament\Resources\PaymentResource;
use App\Models\BankAccount;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Setup;
use Closure;
use Filament\Actions\DeleteAction;
use App\Filament\Tables\Actions\PayBillAction as TablePayBillAction;
use App\Filament\Forms\Actions\PayBillAction as FormPayBillAction;
use Filament\Forms\Form;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Concerns\CanAccessSelectedRecords;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

trait PayBillAction
{
    use CanAccessSelectedRecords;

    protected ?Closure $mutateRecordDataUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'pay';
    }

    public function setUp(): void
    {
        parent::setUp();

        $formSchema = PaymentResource::getSimpleFormSchema();
        $this->label(function (Bill $record) {
            return $record->isPaid() ? 'View Payment' : 'Pay Bill';
        })
            ->icon(function (Bill $record) {
                return $record->isPaid()
                    ? 'heroicon-o-eye'
                    : PaymentMethodEnum::CASH->getIcon();
            })
            ->color(Color::Orange)
            ->modalHeading(function (Bill $record) {
                return $record->isPaid() ? 'Payment Details' : 'Pay Bill';
            })
            ->modalSubmitActionLabel('Pay')
            ->successNotificationTitle('Bill Paid Successfully')
            ->modalWidth('xl')
            ->slideOver()
            ->form(function (Form $form) use ($formSchema): Form {

                return $form->schema($formSchema);
            })
            ->disabledForm(fn(Bill $record) => $record->isPaid())
            ->fillForm(function (Bill $record) use ($formSchema): array {

                if ($record->isPaid()) {
                    $data = $record->payment->toArray();
                } else {
                    foreach ($formSchema as $key => $field) {
                        $data[$field->getName()] = $field->getDefaultState();
                    }
                    $data['amount'] = $record->total;

                }

                if ($this->mutateRecordDataUsing) {
                    $data = $this->evaluate($this->mutateRecordDataUsing, [
                        'record' => $record,
                        'data' => $data,
                    ]);
                }

                return $data;
            })->mutateFormDataUsing(function (Bill $record, array $data) {
                $data['user_id'] = auth()->id();
                $data['amount'] = $record->total;

                if (!empty($data['bank_account_id'])) {
                    $data['account_id'] = BankAccount::find($data['bank_account_id'])?->account_id;
                } elseif (!empty($data['payment_method'])) {
                    $method = PaymentMethodEnum::tryFrom($data['payment_method']);
                    $basicAccount = $method ? BasicAccountsSetupEnum::tryFrom($method->value) : null;
                    $data['account_id'] = $basicAccount ? Setup::getAccountId($basicAccount) : null;
                }

                return $data;
            })
            ->modalFooterActions(function ($record) {
                if ($record->isPaid()) {
                    $action = null;

                    if ($this instanceof \App\Filament\Forms\Header\Actions\PayBillAction) {
                        $action = [
                            DeleteAction::make()
                                ->action(function ($record) {
                                    $record->payment?->delete();
                                    $this->successNotificationTitle('Payment Deleted Successfully');
                                    $this->sendSuccessNotification();
                                })
                                ->icon('heroicon-o-trash')
                                ->requiresConfirmation()
                                ->modalHeading('Delete Payment')
                                ->label('Delete Payment')
                                ->after(fn() => $this->redirect(request()->header('Referer')))
                        ];
                    }
                    if ($this instanceof \App\Filament\Tables\Actions\PayBillAction) {
                        $action = [
                            \Filament\Tables\Actions\DeleteAction::make()
                                ->action(function ($record) {
                                    $record->payment?->delete();
                                    $this->successNotificationTitle('Payment Deleted Successfully');
                                    $this->sendSuccessNotification();
                                })
                                ->label('Delete Payment')
                                ->after(fn() => $this->redirect(request()->header('Referer')))
                        ];
                    }
                    return $action;
                } else {
                    return [static::getModalSubmitAction(), static::getModalCancelAction()];
                }
            })
            ->
            action(function (Bill $record) {


                $data = $this->getFormData();

                DB::transaction(function () use ($record, $data) {
                    $payment = $record->payment()->create($data);
                    $record->update(['payment_id' => $payment->id]);
                    $this->sendSuccessNotification();
                    $payment->save();
                });
            });
    }

    public function mutateRecordDataUsing(?Closure $callback): static
    {
        $this->mutateRecordDataUsing = $callback;
        return $this;
    }

    public function handleBulk(Collection $records): void
    {
        $processed = 0;
        $skipped = 0;

        DB::transaction(function () use ($records, &$processed, &$skipped) {
            $data = $this->getFormData();
            $payment = Payment::createQuietly($data);
            foreach ($records as $record) {
                if ($record->isPaid()) {
                    $skipped++;
                    continue;
                }

                $record->update(['payment_id' => $payment->id]);
                $processed++;
            }
        });

        $this->sendBulkNotification($processed, $skipped);
    }

    protected function sendBulkNotification(int $processed, int $skipped): void
    {
        $message = "{$processed} bill(s) paid.";
        if ($skipped > 0) {
            $message .= " {$skipped} already paid and skipped.";
        }

        $this->successNotificationTitle($message);
        $this->sendSuccessNotification();
    }
}
