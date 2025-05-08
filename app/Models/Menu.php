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
}
