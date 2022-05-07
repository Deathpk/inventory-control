<?php

namespace App\Services\Product;

use App\Exceptions\AbstractException;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Requests\Product\AddQuantityToStockRequest;
use App\Models\History;
use App\Models\Product;
use App\Services\History\HistoryService;

class AddProductQuantityService
{
    private int $productId;
    private int $quantity;

    /**
     * @throws RecordNotFoundOnDatabaseException
     */
    public function addQuantityToStock(AddQuantityToStockRequest $request): void
    {
        $this->setProps($request);
        $product = Product::find($this->productId);

        if (!$product) {
            throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL);
        }

        $product->addQuantity($this->quantity);
        $this->registerAddedQuantityToHistory();
    }

    private function setProps(AddQuantityToStockRequest $request): void
    {
        $this->productId = $request->getProductId();
        $this->quantity  = $request->getQuantityToAdd();
    }

    private function registerAddedQuantityToHistory(): void
    {
        $historyService = new HistoryService();

        $params =  [
            'entityId' => $this->productId,
            'entityType' => History::PRODUCT_ENTITY,
            'changedById' => 1,//TODO DEPOIS DE CRIAR O MODULO DE AUTH , RETIRAR ISSO.
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
