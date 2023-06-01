<?php

namespace App\Services\Product;

use App\Events\Sales\SaleCreated;
use App\Exceptions\AbstractException;
use App\Exceptions\Product\FailedToMarkProductAsSold;
use App\Exceptions\Product\SoldQuantityBiggerThanAvailableQuantity;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Requests\Product\RemoveSoldProductRequest;
use App\Models\Product;
use App\Traits\History\RegisterHistory;
use App\Traits\UsesLoggedEntityId;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Interfaces\CustomException;
use App\Exceptions\ProductNotSellable;

class RemoveSoldProductService
{
    use RegisterHistory;
    use UsesLoggedEntityId;

    private Collection $soldProducts;

    /**
     * @throws RecordNotFoundOnDatabaseException|\Throwable
     */
    public function removeSoldUnitsFromStock(Collection $attributes): void
    {
        $this->setSoldProducts($attributes);

        try {
            DB::beginTransaction();
            $this->resolveProductsSoldUnits();
            DB::commit();
        } catch(CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new FailedToMarkProductAsSold($e);
        }
    }

    private function setSoldProducts(Collection $soldProductList): void
    {
        $this->soldProducts = $soldProductList;
    }


    private function resolveProductsSoldUnits(): void
    {
        $this->soldProducts->each(function (array $soldProduct) {

           $entityId = $soldProduct['productId'] ?? $soldProduct['externalProductId'];
           $product = self::getSoldProduct($entityId);
           
           if(!$product) {
             throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL);
           }

           if(!$product->getCostPrice()) {
             throw new ProductNotSellable();
           }

           $availableQuantity = $product->getQuantity();

           if($availableQuantity < $soldProduct['soldQuantity']) {
             throw new SoldQuantityBiggerThanAvailableQuantity();
           }

           $product->removeProductFromInventory($soldProduct['soldQuantity']);
        });
        
        event(new SaleCreated($this->soldProducts, self::getLoggedCompanyId()));
    }

    private static function getSoldProduct(int|string $entityId): ?Product
    {
        if (is_int($entityId)) {
            return Product::query()->find($entityId);
        }

        return Product::findByExternalId($entityId);
    }
}
