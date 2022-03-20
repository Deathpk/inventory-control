<?php


namespace App\Services\Product;


use App\Exceptions\Product\FailedToCreateOrUpdateProduct;
use App\Exceptions\Product\FailedToDeleteProduct;
use App\Http\Requests\Product\ImportProductsRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Vtiful\Kernel\Excel;

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

    /**
     * @throws \Exception
     */
    public function getProduct(int $id): Builder|Model
    {
        return Product::with(['category', 'brand'])->find($id) ??
            throw new \Exception(
                'Produto não encontrado no banco.'
            );
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
        return Product::with(['category', 'brand'])->get();
    }

    /**
     * @throws \Exception
     */
    public function importProducts(array $products)
    {
        $this->validateFile($products['file']);
        $this->createProductsBasedOnImport($products['file']);
    }

    /**
     * @throws \Exception
     */
    private function validateFile(UploadedFile $file): void
    {
        $isValid = $file->isValid() && in_array($file->extension(), ['xlsx', 'csv']);

        if (!$isValid) {
            throw new \Exception('O Arquivo importado está corrompido ou não é válido.');
        }
        //TODO ADICIONAR CUSTOM EXCEPTION.
    }

    private function createProductsBasedOnImport(UploadedFile $file)
    {
//        $spreadSheet = new Spreadsheet();
        $spreadSheet = IOFactory::load($file->getRealPath());
        dd($spreadSheet);
        //TODO CONTINUE IMPLEMENTATION CHECKING https://phpspreadsheet.readthedocs.io/en/latest/topics/reading-and-writing-to-file/
    }

}
