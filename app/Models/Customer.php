<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Customer extends Model implements AuditableContract
{
    use Auditable;

    protected $fillable = ['name', 'email', 'phone', 'address', 'account_id', 'tax_id'];

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function taxRate(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }
}
