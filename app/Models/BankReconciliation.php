<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class BankReconciliation extends Model implements AuditableContract
{
    use Auditable;

    protected $fillable = [
        'bank_account_id', 'statement_date', 'statement_balance',
        'adjusted_balance', 'is_completed', 'notes'
    ];

    protected $casts = [
        'statement_date' => 'date',
        'statement_balance' => 'decimal:2',
        'adjusted_balance' => 'decimal:2',
        'is_completed' => 'boolean',
    ];

    public function bankAccount(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function bankTransactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BankTransaction::class);
    }
}
