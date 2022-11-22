<?php

namespace App\Services\Product;

use App\Exceptions\Product\AttachmentInvalid;
use App\Exceptions\Product\FailedToImportProducts;
use App\Models\History;
use App\Models\Product;
use App\ValueObjects\Product\ImportedProduct;
use App\Services\History\HistoryService;
use App\Traits\History\RegisterHistory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ImportProductService
{
    use RegisterHistory;

    private UploadedFile $importedFile;
    const MAX_FILE_SIZE_IN_BYTES = 3145728;

    /**
     * @throws FailedToImportProducts|AttachmentInvalid
     */
    public function importProducts(UploadedFile $importedFile): void
    {
        $this->setImportedProductsFile($importedFile);
        $this->validateFile();
        try {
            DB::beginTransaction();
            $this->createProductsBasedOnImport();
            DB::commit();
        } catch(\Throwable $e) {
            DB::rollBack();
            throw new FailedToImportProducts($e);
        }
    }

    private function setImportedProductsFile(UploadedFile $importedFile): void
    {
        $this->importedFile = $importedFile;
    }

    /**
     * @throws AttachmentInvalid
     */
    private function validateFile(): void
    {
        $isValid = $this->importedFile->isValid()
            && in_array($this->importedFile->extension(), ['xlsx', 'csv'])
            && $this->importedFile->getSize() <= self::MAX_FILE_SIZE_IN_BYTES;

        if (!$isValid) {
            throw new AttachmentInvalid();
        }
    }

    private function createProductsBasedOnImport(): void
    {
        $products = $this->convertSpreadsheetToCollection();

        $importedProducts = $products->map(function (array $product) {
            return ImportedProduct::create()->fromArray($product);
        });

        $this->storeImportedProducts($importedProducts);
    }

    private function convertSpreadsheetToCollection(): Collection
    {
        $reader = $this->resolveFileReader();
        $spreadSheet = $reader->load($this->importedFile->getRealPath()); //->getActiveSheet()->toArray()

        return collect($spreadSheet->getActiveSheet()->toArray())->map(function (array $productData) {
            return collect($productData)->filter()->toArray();
        })->forget([0])->filter();
    }

    private function storeImportedProducts(Collection $products): void
    {
        $products->each(function (ImportedProduct $product) {
            $createdProduct = Product::create()->fromRequest($product->toCollection());
            $this->createImportedProductHistory($createdProduct);
        });
    }

    private function resolveFileReader(): Csv|Xlsx
    {
        return $this->importedFile->extension() === 'xlsx'
            ? new Xlsx()
            : new Csv();
    }

    private function createImportedProductHistory(Product &$product): void
    {
        $historyService = new HistoryService();
        $params = [
            'entityId' => $product->getId(),
            'entityType' => History::PRODUCT_ENTITY,
            'changedById' => self::getChangedBy(),
            'metadata' => $this->createHistoryMetaData($product)
        ];

        $historyService->createHistory(History::PRODUCT_UPDATED, $params);
    }

    private function createHistoryMetaData(Product &$product): string
    {
        return collect([
            'entityId' => $product->id,
            'productName' => $product->name,
            'initialQuantity' => $product->quantity,
            'categoryId' => $product->category_id ?? null,
            'brandId' => $product->brand_id ?? null,
            'minimumQuantity' => $product->minimum_quantity
        ])->toJson();
    }
}
