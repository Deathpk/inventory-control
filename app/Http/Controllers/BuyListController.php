<?php

namespace App\Http\Controllers;

use App\Exceptions\FailedToUpdateEntity;
use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Requests\RemoveProductFromBuyListRequest;
use App\Http\Requests\StoreBuyListRequest;
use App\Http\Requests\UpdateBuyListRequest;
use App\Http\Resources\BuyListCollection;
use App\Services\AddProductToBuyListService;
use App\Services\GetBuyListService;
use App\Services\RemoveProductFromBuyListService;
use App\Services\UpdateBuyListProductService;
use Illuminate\Http\JsonResponse;

class BuyListController extends Controller
{
    public function index(GetBuyListService $service): BuyListCollection
    {
        $buyList = $service->showCurrentBuyList();
        return new BuyListCollection($buyList);
    }

    /**
     * @throws RecordNotFoundOnDatabaseException
     */
    public function store(StoreBuyListRequest $request, AddProductToBuyListService $service): JsonResponse
    {
        $service->addToBuyList($request);
        return response()->json([
            'sucess' => true,
            'message' => 'Produto adicionado a lista de compras com sucesso!'
        ]);
    }

    /**
     * @throws FailedToUpdateEntity
     * @throws RecordNotFoundOnDatabaseException
     */
    public function update(UpdateBuyListRequest $request, UpdateBuyListProductService $service): JsonResponse
    {
       $service->updateListItem($request);
       return response()->json([
          'success' => true,
          'message' => 'Item da lista de compras editado com sucesso!'
       ]);
    }

    /**
     * @throws FailedToUpdateEntity
     * @throws RecordNotFoundOnDatabaseException
     */
    public function destroy(RemoveProductFromBuyListRequest $request, RemoveProductFromBuyListService $service): JsonResponse
    {
        $service->removeProductFromBuyList($request);
        return response()->json([
            'success' => true,
            'message' => 'Item da lista de compras removido com sucesso!'
        ]);
    }
}
