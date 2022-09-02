<?php

namespace App\Services\Product;

use App\Exceptions\AbstractException;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Requests\Product\AddQuantityToStockRequest;
use App\Models\History;
use App\Models\Product;
use App\Services\History\HistoryService;
use App\Traits\History\RegisterHistory;
use Illuminate\Support\Collection;
use Throwable;
use App\Exceptions\Product\FailedToAddQuantityToStock;

class AddProductQuantityService
{
    use RegisterHistory;

    private int $entityId;
    private int|string $productId;
    private int $quantity;

    /**
     * @throws RecordNotFoundOnDatabaseException
     */
    public function addQuantityToStock(AddQuantityToStockRequest $request): void
    {
        $this->setProps($request);

        $product = $this->resolveProduct();
        if (!$product) {
            throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL);
        }

        $this->entityId = $product->getId();

        try {
            $product->addQuantity($this->quantity);
        } catch(Throwable $e) {
            throw new FailedToAddQuantityToStock($e);
        }
        
        $this->registerAddedQuantityToHistory();
    }

    private function setProps(AddQuantityToStockRequest $request): void
    {
        $this->productId = $request->getProductId() ?? $request->getExternalProductId();
        $this->quantity = $request->getQuantityToAdd();
    }

    private function resolveProduct(): ?Product
    {
        if (is_integer($this->productId)) {
            return Product::find($this->productId);
        }

        return Product::findByExternalId($this->productId);
    }

    private function registerAddedQuantityToHistory(): void
    {
        $historyService = new HistoryService();

        $params =  [
            'entityId' => $this->entityId,
            'entityType' => History::PRODUCT_ENTITY,
            'changedById' => self::getChangedBy(),
            'metadata' => $this->createHistoryMetaData()
        ];

        $historyService->createHistory(History::ADDED_QUANTITY, $params);
    }

    private function createHistoryMetaData(): string
    {
        return collect([
            'addedQuantity' => $this->quantity
        ])->toJson();
    }

}
