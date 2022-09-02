<?php


namespace App\Services\AutoComplete;


use App\Http\Requests\AutoComplete\AutoCompleteRequest;
use App\Models\Brand;
use App\Services\AutoComplete\Interfaces\AutoCompleteService;
use Illuminate\Support\Collection;
use Throwable;
use App\Exceptions\FailedToRetrieveResults;
use App\Exceptions\AbstractException;

class BrandAutoCompleteService implements AutoCompleteService
{
    public function retrieveResults(AutoCompleteRequest $request): Collection
    {
        try {  
            return Brand::query()->where('name', 'like', "{$request->getInput()}%")->get();
        } catch(Throwable $e) {
            throw new FailedToRetrieveResults(AbstractException::BRAND_ENTITY_LABEL, $e);
        }
    }
}
