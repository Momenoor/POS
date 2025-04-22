<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class BankAccount extends Model implements AuditableContract
{
    use Auditable;

    protected $fillable = [
        'account_id', 'bank_name', 'account_number',
        'routing_number', 'current_balance'
    ];

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function reconciliations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BankReconciliation::class);
    }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BankTransaction::class);
    }
}
