<?php

namespace App\Services;

use App\Exceptions\AbstractException;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Requests\StoreBuyListRequest;
use App\Models\BuyList;
use App\Models\Product;
use App\Traits\UsesLoggedEntityId;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class BuyListService
{
    use UsesLoggedEntityId;
    private Collection $attributes;

    public function showCurrentBuyList()
    {
        $buyList = BuyList::first();
        //TODO TRAZER TODOS OS PRODUTOS COM NOME , QUANTIDADE ATUAL NO ESTOQUE , E A QUANTIDADE PARA COMPRAR. UTILIZAR UM RESOURCE?
    }

    /**
     * @throws RecordNotFoundOnDatabaseException
     */
    public function addToBuyList(StoreBuyListRequest $request): void
    {
        $this->attributes = $request->getAttributes();
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
        $entityId = $this->attributes->get('productId') ?? $this->attributes->get('externalProductId');
        $productExists = is_int($entityId)
            ? Product::find($entityId)
            : Product::findByExternalId($entityId);

        if (!$productExists) {
            throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL, $entityId);
        }
    }

    private static function getCurrentBuyList(): ?BuyList
    {
        return BuyList::first();
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
