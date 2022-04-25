<?php

namespace App\Services\Product;

use App\Exceptions\AbstractException;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Requests\Product\RemoveSoldUnitRequest;
use App\Models\History;
use App\Models\Product;
use App\Models\ProductSalesReport;
use App\Models\User;
use App\Services\History\HistoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RemoveSoldUnitService
{
    private array $attributes;
    private Product|null $product;

    /**
     * @throws RecordNotFoundOnDatabaseException|\Throwable
     */
    public function removeSoldUnit(RemoveSoldUnitRequest $request): void
    {
        $this->setAttributes($request->getAttributes());
        $this->findSelectedProduct();
        try {
            DB::beginTransaction();
            $this->product->removeSoldUnit($this->attributes['soldQuantity']);
            $this->addSaleToSalesReport();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
            //TODO
        }

    }

    private function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    private function findSelectedProduct(): void
    {
        $this->product = Product::find($this->attributes['productId']);
        if (!$this->product) {
            throw new RecordNotFoundOnDatabaseException(AbstractException::PRODUCT_ENTITY_LABEL);
        }
    }

    private function addSaleToSalesReport(): void
    {
        ProductSalesReport::create($this->attributes);
    }

    /**
     * @throws \Throwable
     */
    private function createHistory(array $attributes, int $actionId): void
    {
        //TODO REFATORAR.
        /** @var User $currentLoggedUser */
        $currentLoggedUser = Auth::user();
        $historyService = new HistoryService();

        $params =  [
            'entityId' => $this->attributes['productId'],
            'entityType' => History::PRODUCT_ENTITY,
            'actionId' => $actionId,
            'changedById' => 1,//TODO DEPOIS DE CRIAR O MODULO DE AUTH , RETIRAR ISSO.
            'metadata' => $this->createHistoryMetaData($attributes)
        ];

        $historyService->createProductHistory($params);
    }

    private function createHistoryMetaData(array $data): string
    {//TODO IMPLEMENTAR
        return " ";
    }
}
