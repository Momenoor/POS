<?php

namespace App\Models;

use App\Contracts\Journalable;
use App\Enums\BasicAccountsSetupEnum;
use App\Observers\BillObserver;
use App\Observers\JournalEntryObserver;
use App\Observers\UserIdObserver;
use App\Traits\InteractWithJournalEntry;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use OwenIt\Auditing\Auditable;


#[ObservedBy([UserIdObserver::class, BillObserver::class, JournalEntryObserver::class,])]
class Bill extends Model implements \OwenIt\Auditing\Contracts\Auditable, Journalable
{
    use Auditable, InteractWithJournalEntry;

    protected $fillable = [
        'reference_number', 'description', 'date', 'due_date',
        'supplier_id', 'user_id', 'total', 'payment_id',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'total' => 'decimal:2',
    ];

    protected $with = ['expenses'];

//    protected static function booted()
//    {
//        static::creating(function ($order) {
//            $order->calculateTotals();
//        });
//
//        static::updating(function ($order) {
//            $order->calculateTotals();
//        });
//    }
//
//    public function calculateTotals(): void
//    {
//        $subtotal = $this->expenses()->sum('total');
//        $this->total = (float)$subtotal;
//    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function scopeUnpaid($query)
    {
        return $query->whereNull('payment_id');
    }

    public function scopePaid($query)
    {
        return $query->whereNotNull('payment_id');
    }

    public function isPaid(): bool
    {
        if ($this->relationLoaded('payment')) {
            return $this->payment !== null;
        }
        $this->load('payment');
        return $this->payment()->exists();
    }

    public function getJournalItems(): array
    {
        $this->load('expenses');

        $items = [];
        $calculatedTotal = 0;
        foreach ($this->expenses as $expense) {
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

        if (abs($calculatedTotal - $this->total) > 0.01) {
            throw new \RuntimeException(sprintf(
                'Expense total (%.2f) does not match bill total (%.2f)',
                $calculatedTotal,
                $this->total
            ));
        }

        $items[] = [
            'account_id' => $this->supplier->account_id ??
                Setup::getAccountId(BasicAccountsSetupEnum::ACCOUNTS_PAYABLE),
            'debit' => 0,
            'credit' => $this->total,
            'memo' => $this->description,
            'referenceable_id' => $this->id,
            'referenceable_type' => get_class($this),
        ];

        return $items;
    }

    public function formattedLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->reference_number . ' | ' . $this->date->format('d-m-Y') . ' - [' . number_format($this->total) . ' AED]',
        );
    }
}
