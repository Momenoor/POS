<?php

namespace App\Models;

use App\Contracts\Journalable;
use App\Enums\BankTransactionTypeEnum;
use App\Observers\BankTransactionObserver;
use App\Observers\JournalEntryObserver;
use App\Observers\UserIdObserver;
use App\Traits\InteractWithJournalEntry;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

#[ObservedBy([JournalEntryObserver::class, BankTransactionObserver::class, UserIdObserver::class])]
class BankTransaction extends Model implements AuditableContract, Journalable
{
    use Auditable, InteractWithJournalEntry;

    protected $fillable = [
        'bank_account_id', 'bank_reconciliation_id', 'user_id', 'account_id', 'date',
        'description', 'amount', 'type', 'reference_number'
    ];

    protected $casts = [
        'type' => BankTransactionTypeEnum::class,
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function bankReconciliation(): BelongsTo
    {
        return $this->belongsTo(BankReconciliation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function getJournalItems(): array
    {
        if (!isset($this->type)) {
            throw new \RuntimeException('Bank transaction type must be set');
        }

        if (empty($this->bank_account_id) || empty($this->account_id)) {
            throw new \RuntimeException('Both bank account and counterpart account must be specified');
        }

        $baseItem = [
            'memo' => $this->description ?? 'Bank transaction ' . $this->reference_number,
            'referenceable_id' => $this->id,
            'referenceable_type' => get_class($this),
        ];

        return match ($this->type) {
            BankTransactionTypeEnum::DEPOSIT => [
                array_merge($baseItem, [
                    'account_id' => $this->bankAccount->account_id,
                    'debit' => $this->amount,
                    'credit' => 0,
                ]),
                array_merge($baseItem, [
                    'account_id' => $this->account_id,
                    'debit' => 0,
                    'credit' => $this->amount,
                ])
            ],
            BankTransactionTypeEnum::WITHDRAWAL => [
                array_merge($baseItem, [
                    'account_id' => $this->account_id,
                    'debit' => $this->amount,
                    'credit' => 0,
                ]),
                array_merge($baseItem, [
                    'account_id' => $this->bankAccount->account_id,
                    'debit' => 0,
                    'credit' => $this->amount,
                ])
            ],
            default => throw new \RuntimeException("Unsupported transaction type: {$this->type}"),
        };
    }

}
