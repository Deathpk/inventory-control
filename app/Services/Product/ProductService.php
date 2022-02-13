<?php


namespace App\Services\Product;


use App\Http\Requests\Product\StoreProductRequest;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductService
{
    private Product $product;


    public function __construct()
    {
        //TODO
    }


    /**
     * @throws \Exception
     */
    public function createProduct(StoreProductRequest $request): void
    {
        try {
            DB::beginTransaction();
            $this->storeProduct($request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            //TODO CREATE A CUSTOM EXCEPTION.
        }
    }

    private function storeProduct(StoreProductRequest $request): void
    {
        Product::create()->fromRequest($request);
    }

    public function setProduct(Product $product)
    {
        $this->product = $product;
    }

}
