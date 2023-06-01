<?php

namespace App\Services\Product;

use App\Exceptions\AbstractException;
use App\Exceptions\FailedToDeleteEntity;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Requests\Product\DeleteProductRequest;
use App\Models\History;
use App\Models\Product;
use App\Services\History\HistoryService;
use App\Traits\History\RegisterHistory;
use Illuminate\Support\Collection;

class DeleteProductService
{
    use RegisterHistory;

    private bool $isExternalId;
    private int $productId;

    /**
     * @throws FailedToDeleteEntity
     */
    public function deleteProduct(Collection $attributes): void
    {
        $this->setAttributes($attributes);

        try {
            $product = $this->resolveProduct();
            if (!$product) {
                throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL);
            }
            $product->delete();
            $this->createProductDeletedHistory($product->getId());
            
        } catch (\Throwable $e) {
            throw new FailedToDeleteEntity(AbstractException::PRODUCT_ENTITY_LABEL, $e);
        }
    }

    private function setAttributes(Collection $attributes): void
    {
        $this->isExternalId = (bool) $attributes->get('isExternal');
        $this->productId = $attributes->get('productId');
    }

    private function resolveProduct(): ?Product
    {
        if (!$this->isExternalId) {
            return Product::find($this->productId);
        }

        return Product::findByExternalId($this->productId);
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
            'changedById' => self::getChangedBy(),
            'metadata' => $this->createHistoryMetaData()
        ];

        $historyService->createHistory(History::PRODUCT_DELETED, $params);
    }

    private function createHistoryMetaData(): string
    {
        return " ";
    }
}
