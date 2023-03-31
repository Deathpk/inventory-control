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
        dd($this);
        return [
            'sale_date' => $this->created_at->format('Y-m-d H:i:s'),
            'products' => $this->getSoldProductsDetails(),
            'total_price' => $this->total_price,
            'profit' => $this->profit
        ];
    }

    public function getSoldProductsDetails()
    {
        return json_decode($this->products);
    }
}
