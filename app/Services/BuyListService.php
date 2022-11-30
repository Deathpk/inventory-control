<?php

namespace App\Services;

use App\Exceptions\AbstractException;
use App\Exceptions\FailedToUpdateEntity;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Factories\BuyListProduct;
use App\Http\Requests\RemoveProductFromBuyListRequest;
use App\Http\Requests\StoreBuyListRequest;
use App\Http\Requests\UpdateBuyListRequest;
use App\Models\BuyList;
use App\Models\Product;
use App\Traits\UsesLoggedEntityId;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class BuyListService
{
    use UsesLoggedEntityId;

    private Collection $attributes;
    private int|string $entityId;
    private string $entityIdLabel;

    public function showCurrentBuyList(): Collection
    {
        return $this->getCurrentFormattedBuyList();
    }

    private function getCurrentFormattedBuyList(): Collection
    {
        $originalBuyList = self::getProductsFromBuyList();

        return $originalBuyList->map(function (object $buyListItem) {
           $product = self::getProductDataFromBuyListItem($buyListItem);
           return new BuyListProduct($product);
        });
    }

    private static function getProductDataFromBuyListItem(object $buyListItem): array
    {
        $identificationColumn = isset($buyListItem->productId) ? 'id' : 'external_product_id';
        $identificationValue  = $identificationColumn === 'id' ? $buyListItem->productId : $buyListItem->externalProductId;

        $result = Product::query()->select([
            $identificationColumn,
            'name',
            'quantity',
        ])->where($identificationColumn, '=', $identificationValue)
            ->first()
            ->toArray();

        return array_merge(
            $result,
            ['repositionQuantity' => $buyListItem->repositionQuantity]
        );
    }

    private static function getProductsFromBuyList(): Collection
    {
        return collect(json_decode(self::getCurrentBuyList()->products));
    }

    /**
     * @throws RecordNotFoundOnDatabaseException
     */
    public function addToBuyList(StoreBuyListRequest $request): void
    {
        $this->attributes = $request->getAttributes();
        $this->resolveEntityIdAndLabel();
        $this->checkIfRequiredProductExists();
        $existingBuyList = self::getCurrentBuyList();

        try {
            if (!$existingBuyList) {
                $this->createNewBuyList();
            } else {
                $this->addNewProductToBuyList($existingBuyList);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
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

    private function resolveEntityIdAndLabel (): void
    {
        $this->entityId = $this->attributes->get('productId') ?? $this->attributes->get('externalProductId');
        $this->entityIdLabel = is_int($this->entityId) ? 'productId' : 'externalProductId';
    }

    private static function getCurrentBuyList(): ?BuyList
    {
        return BuyList::first();
    }

    private static function getProductsFromCurrentBuyList(): Collection
    {
        $currentBuyList = self::getCurrentBuyList();
        return collect(json_decode($currentBuyList->products));
    }

    private function createNewBuyList(): void
    {
        BuyList::create()->fromCollection($this->attributes);
    }

    private function addNewProductToBuyList(BuyList $existingBuyList): void
    {
        $existingBuyList->addProductToExistingBuyList($this->attributes);
    }

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
        $updatedBuyListProducts = $this->updateBuyListProductsObject($productsFromCurrentList);
        $buyList = self::getCurrentBuyList();

        $buyList->updateBuyListProduct($updatedBuyListProducts);
    }

    private function updateBuyListProductsObject(Collection $productsFromCurrentList): string
    {
        return $productsFromCurrentList->map(function (object $buyListItem) {
            if ($buyListItem->{$this->entityIdLabel} === $this->entityId) {
                $buyListItem->repositionQuantity = $this->attributes->get('repositionQuantity');
            }
            return $buyListItem;
        })->toJson();
    }

    private function productExistsOnCurrentBuyList(Collection $productsFromCurrentBuyList): void
    {
        $productExistsOnList = $productsFromCurrentBuyList->filter(function (object $buyListProduct) {
            return $buyListProduct->{$this->entityIdLabel} === $this->entityId;
        })->isNotEmpty();

        if (!$productExistsOnList) {
            throw new FailedToUpdateEntity(AbstractException::BUY_LIST_ITEM_ENTITY_LABEL);
        }
    }

    /**
     * @throws FailedToUpdateEntity
     * @throws RecordNotFoundOnDatabaseException
     */
    public function removeProductFromBuyList(RemoveProductFromBuyListRequest $request): void
    {
        $this->attributes = $request->getAttributes();
        $this->resolveEntityIdAndLabel();
        $this->checkIfRequiredProductExists();
        $productsFromCurrentList = self::getProductsFromCurrentBuyList();
        $this->productExistsOnCurrentBuyList($productsFromCurrentList);
        $updatedBuyListProducts = $this->removeItemFromBuyList($productsFromCurrentList);
        $buyList = self::getCurrentBuyList();

        $buyList->updateBuyListProduct($updatedBuyListProducts);
    }

    private function removeItemFromBuyList(Collection $productsFromCurrentList): string
    {
        return $productsFromCurrentList->filter(function (object $buyListProduct) {
           return $buyListProduct->{$this->entityIdLabel} !== $this->entityId;
        })->toJson();
    }


}
