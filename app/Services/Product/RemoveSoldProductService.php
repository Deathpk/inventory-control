<?php

namespace App\Services\Product;

use App\Exceptions\AbstractException;
use App\Exceptions\Product\FailedToMarkProductAsSold;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Requests\Product\RemoveSoldProductRequest;
use App\Models\History;
use App\Models\Product;
use App\Models\ProductSalesReport;
use App\Services\History\HistoryService;
use Illuminate\Support\Facades\DB;

class RemoveSoldProductService
{
    private array $attributes;
    private Product|null $product;

    /**
     * @throws RecordNotFoundOnDatabaseException|\Throwable
     */
    public function removeSoldUnit(RemoveSoldProductRequest $request): void
    {
        $this->setAttributes($request->getAttributes());
        $this->findSelectedProduct();
        try {
            DB::beginTransaction();
            $this->product->removeSoldUnit($this->attributes['soldQuantity']);
            $this->addSaleToSalesReport();
            $this->createSoldHistory();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new FailedToMarkProductAsSold($e);
        }
    }

    private function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    private function findSelectedProduct(): void
    {
        $this->product = Product::find($this->attributes['productId']);
        if (!$this->product) {
            throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL);
        }
    }

    private function addSaleToSalesReport(): void
    {
        ProductSalesReport::create($this->attributes);
    }

    /**
     * @throws \Throwable
     */
    private function createSoldHistory(): void
    {
        $historyService = new HistoryService();

        $params =  [
            'entityId' => $this->attributes['productId'],
            'entityType' => History::PRODUCT_ENTITY,
            'changedById' => 1,//TODO DEPOIS DE CRIAR O MODULO DE AUTH , RETIRAR ISSO.
            'metadata' => $this->createHistoryMetaData()
        ];

        $historyService->createHistory(History::PRODUCT_SOLD, $params);
    }

    private function createHistoryMetaData(): string
    {
        return collect(['entityId' => $this->attributes['productId'], 'changedBy' => 1])->toJson();
    }
}
