<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuCategoryItem extends Model
{
    protected $fillable = [
        'menu_id',
        'category_id',
        'item_id',
        'price',
        'cost',
        'is_available',
        'is_taxable',
        'tax_rate_id',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_taxable' => 'boolean',
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
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
        return $this->belongsTo(TaxRate::class);
    }
}
