<?php


namespace App\Services\Product;


use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Session\Store;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductService
{
    private Product $product;

    /**
     * @throws \Exception
     */
    public function createOrUpdateProduct(StoreProductRequest|UpdateProductRequest $request, $id = null): void
    {
        try {
            DB::beginTransaction();
            switch ($request) {
                case $request instanceof StoreProductRequest : $this->storeProduct($request);
                break;
                case $request instanceof UpdateProductRequest : $this->updateProduct($id, $request);
                break;
            }
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            //TODO CREATE A CUSTOM EXCEPTION.
        }
    }

    private function updateProduct(int $id, UpdateProductRequest $request): void
    {
        $product = Product::find($id);
        if (!$product) {
            throw new \Exception('Produto não encontrado no banco.');
            //TODO JOGAR EXCEÇAO CUSTOM.
        }
        $this->setProduct($product);
        $attributes = $request->getAttributes();
        $this->product->fromRequest($attributes);
    }

    private function storeProduct(StoreProductRequest $request): void
    {
        $attributes = $request->getAttributes();
        Product::create()->fromRequest($attributes);
    }

    public function setProduct(Product $product)
    {
        $this->product = $product;
    }

    public function deleteProduct(int $id): void
    {
        $product = Product::find($id);
        if (!$product) {
            throw new \Exception('Produto não encontrado no banco.');
            //TODO JOGAR EXCEÇAO CUSTOM.
        }

        $product->delete();
    }

}
