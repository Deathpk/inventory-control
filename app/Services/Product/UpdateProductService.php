<?php

namespace App\Services\Product;

use App\Exceptions\AbstractException;
use App\Exceptions\Product\FailedToUpdateProduct;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\History;
use App\Models\Product;
use App\Services\History\HistoryService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class UpdateProductService
{
    private int $id;
    private stdClass $oldAttributes;
    private stdClass $updatedAttributes;

    /**
     * @throws RecordNotFoundOnDatabaseException|FailedToUpdateProduct
     */
    public function updateProduct(int $productId, UpdateProductRequest $request): void
    {
        $this->id = $productId;
        $attributes = $request->getAttributes();
        /** @var Product $product */
        $product = Product::find($productId);
        if (!$product) {
            throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL);
        }

        $this->oldAttributes = (object) $product->toArray();

        try {
            DB::beginTransaction();
            $updatedProduct = $product->fromRequest($attributes);
            $this->updatedAttributes = (object) $updatedProduct->toArray();
            $this->createUpdatedProductHistory();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new FailedToUpdateProduct($e);
        }
    }

    /**
     * @throws \Throwable
     */
    private function createUpdatedProductHistory(): void
    {
        $historyService = new HistoryService();
        $params = [
            'entityId' => $this->id,
            'entityType' => History::PRODUCT_ENTITY,
            'changedById' => 1,//TODO DEPOIS DE CRIAR O MODULO DE AUTH , RETIRAR ISSO.
            'metadata' => $this->createHistoryMetaData()
        ];

        $historyService->createHistory(History::PRODUCT_UPDATED, $params);
    }

    private function createHistoryMetaData(): string
    {
        return collect([
            'entityId' => $this->id,
            'changes' => $this->resolveChanges()
        ]);
    }

    private function resolveChanges(): string
    {
        $changes = collect();
        $changeAbleProperties = collect([
            'name', 'quantity',
            'limit_for_restock', 'paid_price',
            'selling_price', 'category_id',
            'brand_id'
        ]);

        $changeAbleProperties->each(function (string $column) use($changes) {
            $hasChanged = !($this->oldAttributes->{$column} === $this->updatedAttributes->{$column});
            if ($hasChanged) {
                $changes->push($this->getChangedPropertyMap($column));
            }
        });

        return $changes->toJson();
    }

    private function getChangedPropertyMap(string $column): array
    {
        return [
            $column => [
                'from' => $this->oldAttributes->{$column},
                'to' => $this->updatedAttributes->{$column}
            ]
        ];
    }
}
