<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class BankTransaction extends Model implements AuditableContract
{
    use Auditable;

    protected $fillable = [
        'bank_account_id', 'reconciliation_id', 'transaction_date',
        'description', 'amount', 'type', 'reference', 'is_reconciled'
    ];

    protected $casts = [
        'is_reconciled' => 'boolean'
    ];

    public function bankAccount(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function reconciliation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BankReconciliation::class);
    }
}
