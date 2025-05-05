<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use App\Enums\TableStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Table extends Model implements AuditableContract
{
    use  Auditable;

    protected $fillable = [
        'restaurant_id',
        'name',
        'capacity',
        'status',
        'notes'
    ];

    protected $casts = [
        'status' => TableStatusEnum::class,
    ];

    public function restaurant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function currentOrder(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->orders()
                ->whereIn('status', OrderStatusEnum::getOperatingStatuses())
                ->latest()
                ->first()
        );
    }
}
