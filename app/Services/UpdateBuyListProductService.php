<?php

namespace App\Services;

use App\Exceptions\AbstractException;
use App\Exceptions\FailedToUpdateEntity;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Requests\UpdateBuyListRequest;
use App\Models\BuyList;
use App\Models\Product;
use Illuminate\Support\Collection;
use Throwable;

class UpdateBuyListProductService
{
    private Collection $attributes;
    private int|string $entityId;
    private string $entityIdLabel;
    
    /**
     * @throws RecordNotFoundOnDatabaseException|FailedToUpdateEntity
     */
    public function updateListItem(UpdateBuyListRequest $request): void
    {
        $this->attributes = $request->getAttributes();
        $this->resolveEntityIdAndLabel();
        $this->checkIfRequiredProductExists();
        $productsFromCurrentList = self::getProductsFromCurrentBuyList();
        $this->productExistsOnCurrentBuyList($productsFromCurrentList);

        try {
            $updatedBuyListProducts = $this->updateBuyListProductsObject($productsFromCurrentList);
            $buyList = self::getCurrentBuyList();
            $buyList->updateBuyListProduct($updatedBuyListProducts);
        } catch(Throwable $e) {
            throw new FailedToUpdateEntity(AbstractException::BUY_LIST_ITEM_ENTITY_LABEL, $e);
        }
    }

    private function resolveEntityIdAndLabel (): void
    {
        $this->entityId = $this->attributes->get('productId') ?? $this->attributes->get('externalProductId');
        $this->entityIdLabel = is_int($this->entityId) ? 'productId' : 'externalProductId';
    }

    /**
     * @throws RecordNotFoundOnDatabaseException
     */
    private function checkIfRequiredProductExists(): void
    {
        $productExists = $this->entityIdLabel === 'productId'
            ? Product::find($this->entityId)
            : Product::findByExternalId($this->entityId);

        if (!$productExists) {
            throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL, $this->entityId);
        }
    }

    private static function getProductsFromCurrentBuyList(): Collection
    {
        $currentBuyList = self::getCurrentBuyList();
        return collect(json_decode($currentBuyList->products));
    }

    private static function getCurrentBuyList(): ?BuyList
    {
        return BuyList::first();
    }

    private function productExistsOnCurrentBuyList(Collection $productsFromCurrentBuyList): void
    {
        $productExistsOnList = $productsFromCurrentBuyList->filter(function (object $buyListProduct) {
            return property_exists($buyListProduct, $this->entityIdLabel) 
            &&
            $buyListProduct->{$this->entityIdLabel} == $this->entityId;
        })->isNotEmpty();

        if (!$productExistsOnList) {
            throw new FailedToUpdateEntity(AbstractException::BUY_LIST_ITEM_ENTITY_LABEL);
        }
    }

    private function updateBuyListProductsObject(Collection $productsFromCurrentList): string
    {
        return $productsFromCurrentList->map(function (object $buyListItem) {
            if (property_exists($buyListItem, $this->entityIdLabel) && $buyListItem->{$this->entityIdLabel} == $this->entityId) {
                $buyListItem->repositionQuantity = $this->attributes->get('repositionQuantity');
            }
            
            return $buyListItem;
        })->toJson();
    }
}