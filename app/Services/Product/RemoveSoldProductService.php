<?php

namespace App\Services\Product;

use App\Events\Sales\SaleCreated;
use App\Exceptions\Product\FailedToMarkProductAsSold;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Requests\Product\RemoveSoldProductRequest;
use App\Models\Product;
use App\Traits\History\RegisterHistory;
use App\Traits\UsesLoggedEntityId;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RemoveSoldProductService
{
    use RegisterHistory;
    use UsesLoggedEntityId;

    private Collection $soldProducts;

    /**
     * @throws RecordNotFoundOnDatabaseException|\Throwable
     */
    public function removeSoldUnitsFromStock(RemoveSoldProductRequest $request): void
    {
        $this->setSoldProducts($request->getAttributes());

        try {
            DB::beginTransaction();
            $this->resolveProductsSoldUnits();
            DB::commit();
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

           if (!$product) {
               //TODO CRIAR UMA CUSTOM VALIDATION
               throw new NotFoundHttpException('Produto não encontrado na nossa base de dados');
           }

           $product->removeSoldUnit($soldProduct['soldQuantity']);
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
