<?php

namespace App\Http\Resources\Reports;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSalesReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|Arrayable
    {
        return [
            'sale_date' => $this->created_at->format('Y-m-d H:i:s'),
            'product_name' => $this->product->name,
            'sold_quantity' => $this->sold_quantity,
            'cost_price' => $this->cost_price,
            'profit' => $this->profit
        ];
    }
}
