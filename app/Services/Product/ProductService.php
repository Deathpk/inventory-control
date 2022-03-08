<?php


namespace App\Services\Product;


use App\Exceptions\Product\FailedToCreateOrUpdateProduct;
use App\Exceptions\Product\FailedToDeleteProduct;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductService
{
    private Product $product;

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @throws FailedToCreateOrUpdateProduct
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
            throw new FailedToCreateOrUpdateProduct($e);
        }
    }

    /**
     * @throws \Exception
     */
    private function updateProduct(int $id, UpdateProductRequest $request): void
    {
        $product = Product::find($id);
        if (!$product) {
            throw new \Exception('Produto não encontrado no banco.');
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

    /**
     * @throws FailedToDeleteProduct
     */
    public function deleteProduct(int $id): void
    {
        try {
            $product = Product::find($id);
            if (!$product) {
                throw new \Exception('Produto não encontrado no banco.');
            }

            $product->delete();
        } catch (\Throwable $e) {
            throw new FailedToDeleteProduct($e);
        }

    }

    public function listProducts(): Collection
    {
        return Product::all();
    }

}
