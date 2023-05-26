<?php

namespace App\Services;

use App\Exceptions\AbstractException;
use App\Exceptions\FailedToRetrieveResults;
use App\Exceptions\Interfaces\CustomException;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Factories\BuyListProduct;
use App\Models\BuyList;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Throwable;

class GetBuyListService
{
    public function showCurrentBuyList(): Collection
    {
        try {
            return $this->getCurrentFormattedBuyList();
        } 
        catch(CustomException $e) {
            throw $e;
        }
        catch(\Throwable $e) {
            throw new FailedToRetrieveResults(AbstractException::BUY_LIST_ITEM_ENTITY_LABEL, $e);
        }
    }

    private function getCurrentFormattedBuyList(): Collection
    {
        $originalBuyList = collect(json_decode(self::getCurrentBuyList()->products));
        return $originalBuyList->map(function (object $buyListItem) {
           $product = self::getProductDataFromBuyListItem($buyListItem);
           return new BuyListProduct($product);
        });
    }

    private static function getCurrentBuyList(): BuyList
    {
        $currentBuyList = BuyList::first();
        if(!$currentBuyList) {
            throw new RecordNotFoundOnDatabaseException(AbstractException::BUY_LIST_ITEM_ENTITY_LABEL);
        }
        return $currentBuyList;
    }

    private static function getProductDataFromBuyListItem(object $buyListItem): array
    {
        $identificationColumn = isset($buyListItem->productId) ? 'id' : 'external_product_id';
        $identificationValue = $identificationColumn === 'id' ? $buyListItem->productId : $buyListItem->externalProductId;

        $result = Product::query()->select([
            $identificationColumn,
            'name',
            'quantity',
        ])->firstWhere($identificationColumn, '=', $identificationValue)
        ->toArray();

        return array_merge(
            $result,
            ['repositionQuantity' => $buyListItem->repositionQuantity]
        );
    }
}