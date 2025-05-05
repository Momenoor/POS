<?php

namespace App\Models;

use App\Observers\ExpenseObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

#[ObservedBy([ExpenseObserver::class])]
class Expense extends Model implements AuditableContract
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'account_id', 'inventory_item_id', 'bill_id', 'date', 'due_date', 'notes',
        'quantity', 'unit_cost', 'subtotal', 'payment_id'
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'unit_cost' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function journalItem(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(JournalItem::class, 'referenceable');
    }
}
