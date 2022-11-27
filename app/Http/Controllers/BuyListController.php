<?php

namespace App\Http\Controllers;

use App\Exceptions\RecordNotFoundOnDatabaseException;
use App\Http\Requests\RemoveProductFromBuyListRequest;
use App\Http\Requests\StoreBuyListRequest;
use App\Http\Requests\UpdateBuyListRequest;
use App\Http\Resources\BuyListCollection;
use App\Services\BuyListService;
use Illuminate\Http\JsonResponse;

class BuyListController extends Controller
{
    private readonly BuyListService $service;

    public function __construct(BuyListService $service)
    {
        $this->service = $service;
    }

    public function index(): BuyListCollection
    {
        $buyList = $this->service->showCurrentBuyList();
        return new BuyListCollection($buyList);
    }

    /**
     * @throws RecordNotFoundOnDatabaseException
     */
    public function store(StoreBuyListRequest $request): JsonResponse
    {
        $this->service->addToBuyList($request);
        return response()->json([
            'sucess' => true,
            'message' => 'Produto adicionado a lista de compras com sucesso!'
        ]);
    }

    public function update(UpdateBuyListRequest $request)
    {
        //TODO
    }

    public function destroy(RemoveProductFromBuyListRequest $request)
    {
        //TODO
    }
}
