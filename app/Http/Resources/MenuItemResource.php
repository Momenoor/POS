<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $image = ($this->item->image) ? url('storage/' . $this->item->image) : null;
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'menu_id' => $this->menu_id,
            'name' => $this->item->name,
            'description' => $this->item->description,
            'price' => $this->menu_price,
            'is_available' => $this->menu_is_available,
            'image' => $image,
            'options' => $this->item->options,
            'tax_rate' => $this->taxRate->rate??0,
        ];
    }
}
