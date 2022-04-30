<?php

namespace App\Services\Product;

use App\Exceptions\AbstractException;
use App\Exceptions\Product\FailedToDeleteProduct;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Models\History;
use App\Models\Product;
use App\Services\History\HistoryService;

class DeleteProductService
{
    /**
     * @throws FailedToDeleteProduct
     */
    public function deleteProduct(int $id): void
    {
        try {
            $product = Product::find($id);
            if (!$product) {
                throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL);
            }

            $product->delete();
            $this->createProductDeletedHistory($id);
        } catch (\Throwable $e) {
            throw new FailedToDeleteProduct($e);
        }
    }

    /**
     * @throws \Throwable
     */
    private function createProductDeletedHistory(int $productId): void
    {
        $historyService = new HistoryService();

        $params =  [
            'entityId' => $productId,
            'entityType' => History::PRODUCT_ENTITY,
            'changedById' => 1,//TODO DEPOIS DE CRIAR O MODULO DE AUTH , RETIRAR ISSO.
            'metadata' => $this->createHistoryMetaData()
        ];

        $historyService->createHistory(History::PRODUCT_DELETED, $params);
    }

    private function createHistoryMetaData(): string
    {
        return " ";
    }
}
