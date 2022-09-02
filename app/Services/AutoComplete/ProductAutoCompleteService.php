<?php declare(strict_types=1);


namespace App\Services\AutoComplete;

use App\Exceptions\AbstractException;
use App\Exceptions\FailedToRetrieveResults;
use App\Http\Requests\AutoComplete\AutoCompleteRequest;
use App\Models\Product;
use App\Services\AutoComplete\Interfaces\AutoCompleteService;
use Illuminate\Support\Collection;
use Throwable;

class ProductAutoCompleteService implements AutoCompleteService
{
    public function retrieveResults(AutoCompleteRequest $request): Collection
    {
        try {
            return Product::query()->where('name', 'like', "{$request->getInput()}%")->get();
        } catch(Throwable $e) {
            throw new FailedToRetrieveResults(AbstractException::PRODUCT_ENTITY_LABEL, $e);
        }
        
    }
}
