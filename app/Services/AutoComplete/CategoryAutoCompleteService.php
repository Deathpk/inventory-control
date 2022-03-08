<?php declare(strict_types=1);


namespace App\Services\AutoComplete;


use App\Http\Requests\AutoComplete\AutoCompleteRequest;
use App\Models\Category;
use App\Services\AutoComplete\Interfaces\AutoCompleteService;
use Illuminate\Support\Collection;

class CategoryAutoCompleteService implements AutoCompleteService
{

    public function retrieveResults(AutoCompleteRequest $request): Collection
    {
        return Category::query()->where('name', 'like', "{$request->getInput()}%")->get();
    }
}
