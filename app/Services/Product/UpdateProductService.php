<?php

namespace App\Services\Product;

use App\Exceptions\AbstractException;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;

class UpdateProductService
{
    /**
     * @throws RecordNotFoundOnDatabaseException
     */
    public function updateProduct(int $productId, UpdateProductRequest $request): void
    {
        $product = Product::find($productId);
        if (!$product) {
            throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL);
        }
        $product->fromRequest($request->getAttributes());
    }
}
