<?php


namespace App\Services\AutoComplete;


use App\Http\Requests\AutoComplete\AutoCompleteRequest;
use App\Models\Brand;
use App\Services\AutoComplete\Interfaces\AutoCompleteService;
use Illuminate\Support\Collection;

class BrandAutoCompleteService implements AutoCompleteService
{
    public function retrieveResults(AutoCompleteRequest $request): Collection
    {
        return Brand::query()->where('name', 'like', "{$request->getInput()}%")->get();
    }
}
