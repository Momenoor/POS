<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->category_id,
            'menu_id' => $this->menu_id,
            'name' => $this->category->name,
            'description' => $this->description,
            'sort_order' => $this->category->sort_order??0,
            'is_active' => $this->category->is_active,
        ];
    }
}
