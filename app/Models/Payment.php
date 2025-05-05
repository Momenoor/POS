<?php

namespace App\Models;

use App\Contracts\Journalable;
use App\Enums\BasicAccountsSetupEnum;
use App\Enums\PaymentMethodEnum;
use App\Observers\JournalEntryObserver;
use App\Observers\PaymentObserver;
use App\Observers\UserIdObserver;
use App\Traits\InteractWithJournalEntry;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

#[ObservedBy([UserIdObserver::class, PaymentObserver::class, JournalEntryObserver::class])]
class Payment extends Model implements AuditableContract, Journalable
{
    use Auditable, SoftDeletes, InteractWithJournalEntry;


    protected $fillable = [
        'amount', 'payment_date', 'payment_method', 'reference_number', 'user_id', 'notes', 'account_id','bank_account_id'
    ];

    protected $casts = [
        'payment_method' => PaymentMethodEnum::class,
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bankAccount(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }
    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function getJournalItems(): array
    {

        $items = [];
        $calculatedTotal = 0;
        if ($expenses = $this->expenses) {
            foreach ($expenses as $expense) {
                $items[] = [
                    'account_id' => $expense->account_id,
                    'debit' => $expense->subtotal,
                    'credit' => 0,
                    'memo' => $expense->notes,
                    'referenceable_id' => $expense->id,
                    'referenceable_type' => get_class($expense),
                ];
                $calculatedTotal += $expense->subtotal;
            }
        }
        if ($bills = $this->bills) {
            foreach ($bills as $bill) {
                $items[] = ['account_id' => $bill->supplier->account_id ??
                    Setup::getAccountId(BasicAccountsSetupEnum::ACCOUNTS_PAYABLE),
                    'debit' => $bill->total,
                    'credit' => 0,
                    'memo' => $bill->discription,
                    'referenceable_id' => $bill->id,
                    'referenceable_type' => get_class($bill),
                ];
                $calculatedTotal += $bill->total;
            }
        }
        Log::info($bills);
        if (abs($calculatedTotal - $this->amount) > 0.01) {
            throw new \RuntimeException(sprintf(
                'Expense total (%.2f) does not match bill total (%.2f)',
                $calculatedTotal,
                $this->amount
            ));
        }

        $items[] = [
            'account_id' => $this->account_id,
            'debit' => 0,
            'credit' => $this->amount,
            'memo' => $this->notes,
            'referenceable_id' => $this->id,
            'referenceable_type' => get_class($this),
        ];

        return $items;
    }
}


