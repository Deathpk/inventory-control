<?php

namespace App\Services\Product;

use App\Exceptions\Product\FailedToImportProducts;
use App\Models\Product;
use App\Prototypes\Product\ImportedProduct;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ImportProductService
{
    private UploadedFile $importedFile;
    const MAX_FILE_SIZE_IN_BYTES = 3145728;


    /**
     * @throws FailedToImportProducts
     */
    public function importProducts(UploadedFile $importedFile): void
    {
        $this->setImportedProductsFile($importedFile);
        $this->validateFile();
        $this->createProductsBasedOnImport();
    }

    private function setImportedProductsFile(UploadedFile $importedFile): void
    {
        $this->importedFile = $importedFile;
    }

    /**
     * @throws FailedToImportProducts
     */
    private function validateFile(): void
    {
        $isValid = $this->importedFile->isValid()
            && in_array($this->importedFile->extension(), ['xlsx', 'csv'])
            && $this->importedFile->getSize() <= self::MAX_FILE_SIZE_IN_BYTES;

        if (!$isValid) {
            throw new FailedToImportProducts();
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
        $spreadSheet = $reader->load($this->importedFile->getRealPath());

        return collect($spreadSheet->getActiveSheet()->toArray())->map(function (array $productData) {
            return collect($productData)->filter()->toArray();
        })->forget([0])->filter();
    }

    private function storeImportedProducts(Collection $products): void
    {
        $products->each(function (ImportedProduct $product) {
            Product::create()->fromRequest($product->toCollection());
        });
    }

    private function resolveFileReader(): Csv|Xlsx
    {
        return $this->importedFile->extension() === 'xlsx'
            ? new Xlsx()
            : new Csv();
    }
}
