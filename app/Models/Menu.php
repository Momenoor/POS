<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];

    public function menuCategories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MenuCategory::class);
    }

    public function items()
    {
        return $this->belongsToMany(MenuItem::class, 'menu_category_item', 'menu_id', 'item_id')
            ->withPivot('category_id', 'price', 'cost', 'is_available', 'tax_rate_id')
            ->withTimestamps();
    }
}
