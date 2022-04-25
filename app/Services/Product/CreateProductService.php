<?php

namespace App\Services\Product;

use App\Exceptions\Product\FailedToCreateProduct;
use App\Http\Requests\Product\StoreProductRequest;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CreateProductService
{
    /**
     * @throws FailedToCreateProduct
     */
    public function createProduct(StoreProductRequest $request): void
    {
        $attributes = $request->getAttributes();
        try {
            DB::beginTransaction();
            Product::create()->fromRequest($attributes);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new FailedToCreateProduct($e);
        }
    }
}
