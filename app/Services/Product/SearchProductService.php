<?php

namespace App\Services\Product;

use App\Exceptions\AbstractException;
use App\Exceptions\Product\FailedToListProducts;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SearchProductService
{
    /**
     * @throws FailedToListProducts
     */
    public function listProducts(): LengthAwarePaginator
    {
        try {
            return Product::with(['category', 'brand'])->paginate(15);
        } catch (\Throwable $e) {
            throw new FailedToListProducts($e);
        }
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
