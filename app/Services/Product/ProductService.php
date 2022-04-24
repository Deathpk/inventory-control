<?php


namespace App\Services\Product;


use App\Exceptions\Product\FailedToCreateOrUpdateProduct;
use App\Exceptions\Product\FailedToDeleteProduct;
use App\Http\Requests\Product\RemoveSoldUnitRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductSalesReport;
use App\Prototypes\Product\ImportedProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ProductService
{
    private Product $product;
    const MAX_FILE_SIZE_IN_BYTES = 3145728;

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @throws FailedToCreateOrUpdateProduct
     */
    public function createOrUpdateProduct(StoreProductRequest|UpdateProductRequest|Collection $productData, $id = null): void
    {
        try {
            DB::beginTransaction();
            switch ($productData) {
                case $productData instanceof StoreProductRequest : $this->storeProduct($productData);
                break;
                case $productData instanceof UpdateProductRequest : $this->updateProduct($id, $productData);
                break;
                case $productData instanceof Collection : $this->storeImportedProducts($productData);
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

    private function storeImportedProducts(Collection $products)
    {
        $products->each(function (ImportedProduct $product) {
            Product::create()->fromRequest($product->toCollection());
        });
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
        $isValid = $file->isValid()
            && in_array($file->extension(), ['xlsx', 'csv'])
            && $file->getSize() <= self::MAX_FILE_SIZE_IN_BYTES;

        if (!$isValid) {
            throw new \Exception('O Arquivo importado está corrompido ou não é válido.');
        }
        //TODO ADICIONAR CUSTOM EXCEPTION.
    }

    /**
     * @throws FailedToCreateOrUpdateProduct
     */
    private function createProductsBasedOnImport(UploadedFile $file): void
    {
        $products = $this->convertSpreadsheetToArray($file);

        $importedProducts = $products->map(function (array $product) {
            return ImportedProduct::create()->fromArray($product);
        });

        $this->createOrUpdateProduct($importedProducts);
    }

    private function convertSpreadsheetToArray(UploadedFile $file): Collection
    {
        $reader = self::resolveFileReader($file);
        $spreadSheet = $reader->load($file->getRealPath());
        return collect($spreadSheet->getActiveSheet()->toArray())->map(function (array $productData) {
            return collect($productData)->filter()->toArray();
        })->forget([0])->filter();
    }

    private static function resolveFileReader(UploadedFile &$file): Csv|Xlsx
    {
        return $file->extension() === 'xlsx'
            ? new Xlsx()
            : new Csv();
    }

    public function removeSoldUnit(RemoveSoldUnitRequest $request): void
    {
        $attributes = $request->getAttributes();
        /** @var Product $product */
        $product = Product::find($attributes['productId']);

        if (!$product) {
            throw new \Exception('Produto não encontrado na base de dados.');
            //TODO VC JÁ SABE OQUE FAZER...
        }

        try {
            DB::beginTransaction();
            $product->removeSoldUnit($attributes['soldQuantity']);
            $this->addSaleToSalesReport($attributes);
            //TODO ADICIONAR HISTÓRICO DE VENDA.
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function addSaleToSalesReport(array $attributes): void
    {
        //TODO TESTAR A DIFERENCA DE PERFORMANCE PASSANDO POR REFERENCIA , SÓ POR CURIOSIDADE KKKK
        ProductSalesReport::create($attributes);
    }

}
