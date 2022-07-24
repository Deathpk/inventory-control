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
use App\Traits\History\RegisterHistory;
use App\Traits\UsesLoggedEntityId;
use Illuminate\Support\Facades\DB;

class RemoveSoldProductService
{
    use RegisterHistory;
    use UsesLoggedEntityId;

    private array $attributes;
    private Product|null $product;

    /**
     * @throws RecordNotFoundOnDatabaseException|\Throwable
     */
    public function removeSoldUnit(RemoveSoldProductRequest $request): void
    {
        $this->setAttributes($request->getAttributes());
        try {
            DB::beginTransaction();
            $this->findSelectedProduct();
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
        $this->setProduct();
        if (!$this->product) {
            throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL);
        }
    }

    private function setProduct(): void
    {
        if (isset($this->attributes['productId'])) {
            $this->product = Product::find($this->attributes['productId']);
        } else {
            $this->product = Product::findByExternalId($this->attributes['externalProductId']);
        }
    }

    private function addSaleToSalesReport(): void
    {
        ProductSalesReport::create()->fromArray($this->attributes);
    }

    /**
     * @throws \Throwable
     */
    private function createSoldHistory(): void
    {
        $historyService = new HistoryService();

        $params =  [
            'entityId' => $this->product->getId(),
            'entityType' => History::PRODUCT_ENTITY,
            'changedById' => self::getChangedBy(),
            'metadata' => $this->createHistoryMetaData()
        ];

        $historyService->createHistory(History::PRODUCT_SOLD, $params);
    }

    private function createHistoryMetaData(): string
    {
        return collect([
            'entityId' => $this->product->getId()
            , 'changedBy' => self::getChangedBy()
        ])->toJson();
    }
}
