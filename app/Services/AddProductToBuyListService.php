<?php

namespace App\Services;

use App\Exceptions\AbstractException;
use App\Exceptions\EntityAlreadyExistsOnContext;
use App\Exceptions\FailedToCreateEntity;
use App\Exceptions\Interfaces\CustomException;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Requests\StoreBuyListRequest;
use App\Models\BuyList;
use App\Models\Product;
use App\Traits\UsesLoggedEntityId;
use Illuminate\Support\Collection;
use Throwable;

class AddProductToBuyListService
{
    use UsesLoggedEntityId;

    private Collection $attributes;
    private int|string $entityId;
    private string $entityIdLabel;

    /**
     * @throws RecordNotFoundOnDatabaseException
     */
    public function addToBuyList(StoreBuyListRequest $request): void
    {
        $this->attributes = $request->getAttributes();
        $this->resolveEntityIdAndLabel();
        $this->checkIfRequiredProductExists();

        try {
            $existingBuyList = BuyList::first();

            if (!$existingBuyList) {
                $this->createNewBuyList();
                return;
            }
            $this->checkIfProductAlreadyExistsOnList($existingBuyList);
            $this->addNewProductToBuyList($existingBuyList);

        } 
        catch(CustomException $e) {
            throw $e;
        }
        catch(Throwable $e) {
            Throw new FailedToCreateEntity(AbstractException::BUY_LIST_ITEM_ENTITY_LABEL, $e);
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

    private function createNewBuyList(): void
    {
        BuyList::create()->fromCollection($this->attributes);
    }

    private function checkIfProductAlreadyExistsOnList(BuyList $existingBuyList): void
    {
        $productsOnList = (collect(json_decode($existingBuyList->products)));

        $productAlreadyExists = $productsOnList->contains(function($value, $key) {
            return property_exists($value, $this->entityIdLabel) 
            &&
            $value->{$this->entityIdLabel} == $this->entityId; 
        });
        
        if ($productAlreadyExists) {
            throw new EntityAlreadyExistsOnContext(AbstractException::BUY_LIST_ITEM_ENTITY_LABEL);
        }
    }

    private function addNewProductToBuyList(BuyList $existingBuyList): void
    {
        $existingBuyList->addProductToExistingBuyList($this->attributes);
    }
}