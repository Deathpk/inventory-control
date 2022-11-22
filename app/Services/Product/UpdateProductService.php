<?php

namespace App\Services\Product;

use App\Exceptions\AbstractException;
use App\Exceptions\FailedToUpdateEntity;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\History;
use App\Models\Product;
use App\Services\History\HistoryService;
use App\Traits\History\RegisterHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class UpdateProductService
{
    use RegisterHistory;

    private Collection $requestAttributes;
    private stdClass $oldAttributes;
    private stdClass $updatedAttributes;
    private int|string $entityId;

    /**
     * @throws RecordNotFoundOnDatabaseException|FailedToUpdateEntity
     */
    public function updateProduct(UpdateProductRequest $request): void
    {
        $this->requestAttributes = $request->getAttributes();

        $product = $this->resolveProduct();

        if (!$product) {
            throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL);
        }

        $this->oldAttributes = (object) $product->toArray();

        try {
            DB::beginTransaction();
            $updatedProduct = $product->fromRequest($this->requestAttributes);
            $this->updatedAttributes = (object) $updatedProduct->toArray();
            $this->createUpdatedProductHistory();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new FailedToUpdateEntity(AbstractException::PRODUCT_ENTITY_LABEL, $e);
        }
    }

    private function resolveProduct(): ?Product
    {
        $product = Product::find($this->requestAttributes->get('productId'))
            ??
            Product::findByExternalId($this->requestAttributes->get('externalProductId'));

        $this->entityId = $product->getId() ?? null;

        return $product;
    }

    /**
     * @throws \Throwable
     */
    private function createUpdatedProductHistory(): void
    {
        $historyService = new HistoryService();
        $params = [
            'entityId' => $this->entityId,
            'entityType' => History::PRODUCT_ENTITY,
            'changedById' => self::getChangedBy(),
            'metadata' => $this->createHistoryMetaData()
        ];

        $historyService->createHistory(History::PRODUCT_UPDATED, $params);
    }

    private function createHistoryMetaData(): string
    {
        return collect([
            'entityId' => $this->entityId,
            'changes' => $this->resolveChanges()
        ]);
    }

    private function resolveChanges(): string
    {
        $changes = collect();
        $changeAbleProperties = collect([
            'name', 'quantity',
            'minimum_quantity', 'paid_price',
            'selling_price', 'category_id',
            'brand_id', 'external_product_id'
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
