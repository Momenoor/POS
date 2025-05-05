<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class InventoryItem extends Model implements AuditableContract
{
    use Auditable;

    protected $fillable = [
        'name', 'unit', 'current_quantity', 'alert_quantity'
    ];

    protected $casts = [
        'current_quantity' => 'decimal:3',
        'alert_quantity' => 'decimal:3',
    ];

    public function inventoryTransactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
