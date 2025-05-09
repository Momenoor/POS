<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class MenuCategory extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $fillable = [
        'restaurant_id',
        'name',
        'account_id',
        'description',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function menuItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MenuItem::class, 'category_id');;
    }

    public function menu(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
    public function restaurant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
{
    return $this->belongsTo(Restaurant::class);
}

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function items()
    {
        return $this->belongsToMany(MenuItem::class, 'menu_category_item')
            ->withPivot('menu_id', 'menu_price', 'menu_cost', 'menu_is_available', 'menu_tax_rate_id')
            ->withTimestamps();
    }
}
