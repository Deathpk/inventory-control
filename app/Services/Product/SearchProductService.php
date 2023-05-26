<?php

namespace App\Services\Product;

use App\Exceptions\AbstractException;
use App\Exceptions\Interfaces\CustomException;
use App\Exceptions\Product\FailedToListProducts;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SearchProductService
{
    /**
     * @throws FailedToListProducts
     */
    public function listProducts(): LengthAwarePaginator|Builder|Collection
    {
        try {
            return Product::with(['category', 'brand'])->paginate(15);
        } 
        catch(CustomException $e) {
            throw $e;
        }
        catch (\Throwable $e) {
            throw new FailedToListProducts($e);
        }
    }

    /**
     * @throws RecordNotFoundOnDatabaseException
     */
    public function getSpecificProduct(int $id): ProductResource
    {
        $specificProduct = Product::with(['category', 'brand'])->find($id);
        if (!$specificProduct) {
            throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL);
        }

        return ProductResource::make($specificProduct);
    }
}
