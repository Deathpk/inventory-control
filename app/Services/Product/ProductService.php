<?php
//
//
//namespace App\Services\Product;
//
//
//use App\Exceptions\AbstractException;
//use App\Exceptions\FailedToCreateEntity;
//use App\Exceptions\FailedToDeleteEntity;
//use App\Exceptions\RecordNotFoundOnDatabaseException;
//use App\Factories\Product\ImportedProduct;
//use App\Http\Requests\Product\RemoveSoldProductRequest;
//use App\Http\Requests\Product\StoreProductRequest;
//use App\Http\Requests\Product\UpdateProductRequest;
//use App\Models\History;
//use App\Models\Product;
//use App\Models\ProductSalesReport;
//use App\Models\User;
//use App\Services\History\HistoryService;
//use Illuminate\Database\Eloquent\Builder;
//use Illuminate\Database\Eloquent\Model;
//use Illuminate\Http\UploadedFile;
//use Illuminate\Support\Collection;
//use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\DB;
//use PhpOffice\PhpSpreadsheet\Reader\Csv;
//use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
//
//class ProductService
//{
//    private Product $product;
//    private HistoryService $historyService;
//    const MAX_FILE_SIZE_IN_BYTES = 3145728;
//
//    public function __construct(HistoryService $historyService)
//    {
//        $this->historyService = $historyService;
//    }
//
//    public function setProduct(Product $product): void
//    {
//        $this->product = $product;
//    }
//
//    /**
//     * @throws FailedToCreateOrUpdateProduct
//     */
//    public function createOrUpdateProduct(StoreProductRequest|UpdateProductRequest|Collection $productData, $id = null): void
//    {
//        try {
//            DB::beginTransaction();
//            switch ($productData) {
//                case $productData instanceof StoreProductRequest : $this->storeProduct($productData);
//                break;
//                case $productData instanceof UpdateProductRequest : $this->updateProduct($id, $productData);
//                break;
//                case $productData instanceof Collection : $this->storeImportedProducts($productData);
//                break;
//            }
//            DB::commit();
//
//        } catch (\Exception $e) {
//            DB::rollBack();
//            throw new FailedToCreateOrUpdateProduct($e);
//        }
//    }
//
//    /**
//     * @throws \Exception
//     */
//    private function updateProduct(int $id, UpdateProductRequest $request): void
//    {
//        $product = Product::find($id);
//        if (!$product) {
//            throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL);
//        }
//        $this->setProduct($product);
//        $attributes = $request->getAttributes();
//        $this->product->fromRequest($attributes);
//    }
//
//    private function storeImportedProducts(Collection $products): void
//    {
//        $products->each(function (ImportedProduct $product) {
//            Product::create()->fromRequest($product->toCollection());
//        });
//    }
//
//    /**
//     * @throws \Exception
//     */
//    public function getProduct(int $id): Builder|Model
//    {
//        return Product::with(['category', 'brand'])->find($id) ??
//            throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL);
//    }
//
//    private function storeProduct(StoreProductRequest $request): void
//    {
//        $attributes = $request->getAttributes();
//        Product::create()->fromRequest($attributes);
//    }
//
//    /**
//     * @throws FailedToDeleteEntity
//     */
//    public function deleteProduct(int $id): void
//    {
//        try {
//            $product = Product::find($id);
//            if (!$product) {
//                throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL);
//            }
//
//            $product->delete();
//        } catch (\Throwable $e) {
//            throw new FailedToDeleteEntity(AbstractException::PRODUCT_ENTITY_LABEL, $e);
//        }
//
//    }
//
//    public function listProducts(): Collection
//    {
//        return Product::with(['category', 'brand'])->get();
//    }
//
//    /**
//     * @throws \Exception
//     */
//    public function importProducts(array $products): void
//    {
//        $this->validateFile($products['file']);
//        $this->createProductsBasedOnImport($products['file']);
//    }
//
//    /**
//     * @throws \Exception
//     */
//    private function validateFile(UploadedFile $file): void
//    {
//        $isValid = $file->isValid()
//            && in_array($file->extension(), ['xlsx', 'csv'])
//            && $file->getSize() <= self::MAX_FILE_SIZE_IN_BYTES;
//
//        if (!$isValid) {
//            throw new \Exception('O Arquivo importado está corrompido ou não é válido.');
//        }
//        //TODO ADICIONAR CUSTOM EXCEPTION.
//    }
//
//    /**
//     * @throws FailedToCreateEntity
//     */
//    private function createProductsBasedOnImport(UploadedFile $file): void
//    {
//        $products = $this->convertSpreadsheetToArray($file);
//
//        $importedProducts = $products->map(function (array $product) {
//            return ImportedProduct::create()->fromArray($product);
//        });
//
//        $this->createOrUpdateProduct($importedProducts);
//    }
//
//    private function convertSpreadsheetToArray(UploadedFile $file): Collection
//    {
//        $reader = self::resolveFileReader($file);
//        $spreadSheet = $reader->load($file->getRealPath());
//        return collect($spreadSheet->getActiveSheet()->toArray())->map(function (array $productData) {
//            return collect($productData)->filter()->toArray();
//        })->forget([0])->filter();
//    }
//
//    private static function resolveFileReader(UploadedFile &$file): Csv|Xlsx
//    {
//        return $file->extension() === 'xlsx'
//            ? new Xlsx()
//            : new Csv();
//    }
//
//    /**
//     * @throws RecordNotFoundOnDatabaseException|\Throwable
//     */
//    public function removeSoldUnit(RemoveSoldProductRequest $request): void
//    {
//        $attributes = $request->getAttributes();
//        /** @var Product $product */
//        $product = Product::find($attributes['productId']);
//
//        if (!$product) {
//            throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL);
//        }
//
//        try {
//            DB::beginTransaction();
//            $product->removeSoldUnit($attributes['soldQuantity']);
//            $this->addSaleToSalesReport($attributes);
//            $this->createHistory($attributes, History::PRODUCT_SOLD);
//            DB::commit();
//        } catch (\Throwable $e) {
//            DB::rollBack();
//            throw $e;
//        }
//    }
//
//    /**
//     * @throws \Throwable
//     */
//    private function createHistory(array $attributes, int $actionId): void
//    {
//        /** @var User $currentLoggedUser */
//        $currentLoggedUser = Auth::user();
//
//        $params =  [
//            'entityId' => $attributes['productId'],
//            'entityType' => History::PRODUCT_ENTITY,
//            'actionId' => $actionId,
//            'changedById' => 1,//TODO DEPOIS DE CRIAR O MODULO DE AUTH , RETIRAR ISSO.
//            'metadata' => $this->createHistoryMetaData($attributes)
//        ];
//
//        $this->historyService->createProductHistory($params);
//    }
//
//    private function createHistoryMetaData(array $data): string
//    {//TODO
//        return " ";
//    }
//
//    private function addSaleToSalesReport(array $attributes): void
//    {
//        ProductSalesReport::create($attributes);
//    }
//
//}
