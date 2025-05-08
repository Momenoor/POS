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
            'restaurant_id' => $this->resturant_id,
            'name' => $this->name,
            'account_id' => $this->account_id,
            'description' => $this->description,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'items' => MenuItemResource::collection($this->whenLoaded('menuItems')),
        ];
    }
}
