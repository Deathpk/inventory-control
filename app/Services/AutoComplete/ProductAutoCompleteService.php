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
    private Collection $results;
    private string $input;

    public function retrieveResults(AutoCompleteRequest $request): Collection
    {
        try {
            $this->input = $request->getInput();
            $this->resolveProductResults();
            return $this->results;
        } catch(Throwable $e) {
            throw new FailedToRetrieveResults(AbstractException::PRODUCT_ENTITY_LABEL, $e);
        }
    }

    private function resolveProductResults(): void
    {
        $requiredResultKeys = ['id', 'external_product_id', 'name', 'quantity', 'selling_price'];

        $this->results = Product::query()
        ->where('name', 'like', "{$this->input}%")
        ->orWhere('id', 'like', "{$this->input}%")
        ->orWhere('external_product_id', 'like', "{$this->input}%")
        ->get($requiredResultKeys);
    }
}
