<?php

namespace App\Services;

use App\Exceptions\AbstractException;
use App\Exceptions\FailedToCreateEntity;
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

        try{
            $existingBuyList = BuyList::first();

            if (!$existingBuyList) {
                $this->createNewBuyList();
                return;
            }
            $this->addNewProductToBuyList($existingBuyList);

        } catch(Throwable $e) {
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

    private function addNewProductToBuyList(BuyList $existingBuyList): void
    {
        $existingBuyList->addProductToExistingBuyList($this->attributes);
    }
}