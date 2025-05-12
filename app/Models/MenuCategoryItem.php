<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MenuCategoryItem extends Pivot
{
    protected $fillable = [
        'menu_id',
        'category_id',
        'item_id',
        'menu_price',
        'menu_cost',
        'menu_is_available',
        'menu_is_taxable',
        'menu_tax_rate_id',
    ];

    protected $casts = [
        'menu_is_available' => 'boolean',
        'menu_is_taxable' => 'boolean',
        'menu_price' => 'decimal:2',
        'menu_cost' => 'decimal:2',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'item_id');
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class, 'menu_tax_rate_id');
    }
}
