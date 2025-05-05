<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class BankAccount extends Model implements AuditableContract
{
    use Auditable;

    protected $fillable = [
        'account_id', 'bank_name', 'account_number',
        'routing_number', 'current_balance', 'is_primary'
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
    ];

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function bankReconciliations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BankReconciliation::class);
    }

    public function bankTransactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BankTransaction::class);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

}
