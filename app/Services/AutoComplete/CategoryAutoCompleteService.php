<?php declare(strict_types=1);


namespace App\Services\AutoComplete;


use App\Http\Requests\AutoComplete\AutoCompleteRequest;
use App\Models\Category;
use App\Services\AutoComplete\Interfaces\AutoCompleteService;
use Illuminate\Support\Collection;
use Throwable;
use App\Exceptions\AbstractException;
use App\Exceptions\FailedToRetrieveResults;

class CategoryAutoCompleteService implements AutoCompleteService
{

    public function retrieveResults(AutoCompleteRequest $request): Collection
    {
        try { 
            return Category::query()->where('name', 'like', "{$request->getInput()}%")->get();
        } catch(Throwable $e) {
            throw new FailedToRetrieveResults(AbstractException::CATEGORY_ENTITY_LABEL, $e);
        }
    }
}
