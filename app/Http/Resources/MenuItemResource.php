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
        return [
            'id'=>$this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'cost' => $this->cost,
            'account_id' => $this->account_id,
            'is_taxable' => $this->is_taxable,
            'is_available' => $this->is_available,
            'image' => $this->image,
            'options' => $this->options,
            'tax_rate_id' => $this->tax_rate_id
        ];
    }
}
