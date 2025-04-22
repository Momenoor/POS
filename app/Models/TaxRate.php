<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class TaxRate extends Model implements AuditableContract
{
    use Auditable;

    protected $fillable = ['name', 'rate', 'is_active', 'description'];

    public function restaurants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Restaurant::class, 'default_tax_rate_id');;
    }
}
