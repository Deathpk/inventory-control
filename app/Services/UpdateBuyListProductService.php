<?php

namespace App\Services;

use App\Exceptions\AbstractException;
use App\Exceptions\EntityDontExistsOnContext;
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
        $this->checkIfproductExistsOnCurrentBuyList($productsFromCurrentList);

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

    private function checkIfproductExistsOnCurrentBuyList(Collection $productsFromCurrentBuyList): void
    {
        $productExistsOnList = $productsFromCurrentBuyList->contains(function($value, $key) {
            return property_exists($value, $this->entityIdLabel) 
            &&
            $value->{$this->entityIdLabel} == $this->entityId; 
        });
        
        if (!$productExistsOnList) {
            throw new EntityDontExistsOnContext(AbstractException::BUY_LIST_ITEM_ENTITY_LABEL);
        }
    }

    private function updateBuyListProductsObject(Collection $productsFromCurrentList): string
    {
        return $productsFromCurrentList->map(function (object $buyListItem) {
            $itemExistsOnList = property_exists($buyListItem, $this->entityIdLabel) && $buyListItem->{$this->entityIdLabel} == $this->entityId;
            
            if ($itemExistsOnList) {
                $buyListItem->repositionQuantity = $this->attributes->get('repositionQuantity');
            }
            return $buyListItem;
        })->toJson();
    }
}