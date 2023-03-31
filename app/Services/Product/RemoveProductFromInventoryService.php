<?php

namespace App\Services\Product;

use App\Events\Products\UnitRemovedFromInventory;
use App\Exceptions\AbstractException;
use App\Exceptions\Product\FailedToMarkProductAsSold;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Models\Product;
use App\Traits\History\RegisterHistory;
use App\Traits\UsesLoggedEntityId;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Interfaces\CustomException;
use App\Exceptions\Product\QuantityToRemoveBiggerThanAvailableQuantity;
use App\Http\Requests\Products\RemoveProductFromInventoryRequest;

class RemoveProductFromInventoryService
{
    use RegisterHistory;
    use UsesLoggedEntityId;

    private Collection $products;

    /**
     * @throws RecordNotFoundOnDatabaseException|\Throwable
     */
    public function removeUnitsFromStock(RemoveProductFromInventoryRequest $request): void
    {
        $this->setProducts($request->getAttributes());

        try {
            DB::beginTransaction();
            $this->removeProductsFromInventory();
            DB::commit();
        } catch(CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new FailedToMarkProductAsSold($e);
        }
    }

    private function setProducts(Collection $products): void 
    {
        $this->products = $products;
    }

    private function removeProductsFromInventory()
    {
        $this->products->each(function(array $product) {
            $entityId = $product['productId'] ?? $product['externalProductId'];
            $productEntity = self::getProductInstance($entityId);

            if (!$productEntity) {
                throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL);
            }

           $availableQuantity = $productEntity->getQuantity();

            if ($availableQuantity < $product['quantityToRemove']) {
                throw new QuantityToRemoveBiggerThanAvailableQuantity();
            }

          $productEntity->removeProductFromInventory($product['quantityToRemove']);
        });

        event(new UnitRemovedFromInventory($this->products, self::getLoggedCompanyId()));
    }
    

    private static function getProductInstance(int|string $entityId): ?Product
    {
        if (is_int($entityId)) {
            return Product::query()->find($entityId);
        }

        return Product::findByExternalId($entityId);
    }
}
