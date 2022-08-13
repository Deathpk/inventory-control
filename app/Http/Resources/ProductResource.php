<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public static $wrap = 'product';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'external_product_id' => $this->external_product_id,
            'name' => $this->name,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'paid_price' => $this->paid_price,
            'selling_price' => $this->selling_price,
            'limit_for_restock' => $this->limit_for_restock,
            'category' => CategoryResource::make($this->category),
            'brand' => BrandResource::make($this->brand)
        ];
    }
}
