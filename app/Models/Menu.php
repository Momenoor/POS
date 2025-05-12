<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];
    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function menuCategories(): \Illuminate\Database\Eloquent\Relations\belongsToMany
    {
        return $this->belongsToMany(MenuCategory::class, 'menu_category_item', 'menu_id', 'category_id')
            ->withTimestamps();
    }

    public function categoryItems()
    {
        return $this->belongsToMany(MenuItem::class, 'menu_category_item', 'menu_id', 'item_id')
            ->withPivot('category_id', 'menu_price', 'menu_cost', 'menu_is_available', 'menu_is_taxable', 'menu_tax_rate_id')
            ->with(['taxRate','category'])
            ->withTimestamps();
    }
}
