<?php

namespace App\Services;

use App\Exceptions\AbstractException;
use App\Exceptions\EntityDontExistsOnContext;
use App\Exceptions\FailedToDeleteEntity;
use App\Exceptions\FailedToUpdateEntity;
use App\Exceptions\Interfaces\CustomException;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Factories\BuyListProduct;
use App\Http\Requests\RemoveProductFromBuyListRequest;
use App\Http\Requests\StoreBuyListRequest;
use App\Http\Requests\UpdateBuyListRequest;
use App\Models\BuyList;
use App\Models\Product;
use App\Traits\UsesLoggedEntityId;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class RemoveProductFromBuyListService
{
    private bool $isExternal;
    private string|int $entityId;
    private string $entityIdLabel;

        /**
     * @throws FailedToUpdateEntity
     * @throws RecordNotFoundOnDatabaseException
     */
    public function removeProductFromBuyList(RemoveProductFromBuyListRequest $request): void
    {
        $this->isExternal = $request->getIsExternal();
        $this->entityId = $request->getProductId();
        $this->entityIdLabel = $this->isExternal ? 'externalProductId' : 'productId';

        try {
            $this->checkIfRequiredProductExists();
            $productsFromCurrentList = self::getProductsFromCurrentBuyList();
            $this->checkIfproductExistsOnCurrentBuyList($productsFromCurrentList);
            $updatedBuyListProducts = $this->removeItemFromBuyList($productsFromCurrentList);
            $buyList = self::getCurrentBuyList();
            $buyList->updateBuyListProduct($updatedBuyListProducts);
        } catch(CustomException $e) {
            throw $e;
        }
        catch(Throwable $e) {
            throw new FailedToDeleteEntity(AbstractException::BUY_LIST_ITEM_ENTITY_LABEL, $e);
        }

    }

    private function checkIfRequiredProductExists(): void
    {
        $productExists = $this->isExternal 
        ? Product::findByExternalId($this->entityId)
        : Product::find($this->entityId);
       
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

    private function removeItemFromBuyList(Collection $productsFromCurrentList): string
    {
        return $productsFromCurrentList->filter(function (object $buyListProduct) {
            return $this->shouldRemoveThisItem($buyListProduct);
        })->toJson();
    }

    private function shouldRemoveThisItem(object $buyListProduct): bool
    {
        return !(property_exists($buyListProduct, $this->entityIdLabel) && $buyListProduct->{$this->entityIdLabel} == $this->entityId);
    }
}
