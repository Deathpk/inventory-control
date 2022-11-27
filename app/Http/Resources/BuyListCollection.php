<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BuyListCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'buyList' => $this->collection
        ];
    }
}
