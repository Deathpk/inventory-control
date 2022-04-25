<?php

namespace App\Services\Product;

use App\Exceptions\AbstractException;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SearchProductService
{
    public function listProducts(): Collection
    {
        return Product::with(['category', 'brand'])->get();
    }

    /**
     * @throws RecordNotFoundOnDatabaseException
     */
    public function getSpecificProduct(int $id): Builder|Model
    {
        return Product::with(['category', 'brand'])->find($id) ??
            throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL);
    }
}
