<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MenuItem extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable, LogsActivity;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'cost',
        'account_id',
        'is_taxable',
        'is_available',
        'image',
        'options',
        'tax_rate_id'
    ];

    protected $casts = [
        'options' => 'array',
        'is_taxable' => 'boolean',
        'is_available' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'price', 'is_available', 'cost', 'tax_rate_id'])
            ->logOnlyDirty();
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');;
    }

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function taxRate(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }
}
