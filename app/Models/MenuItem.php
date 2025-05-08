<?php

namespace App\Models;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 *
 */
class MenuItem extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

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
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

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

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_category_item')
            ->withPivot('category_id', 'price', 'cost', 'is_available', 'tax_rate_id')
            ->withTimestamps();
    }
}
