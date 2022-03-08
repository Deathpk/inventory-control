<?php declare(strict_types=1);


namespace App\Services\AutoComplete\Interfaces;

use App\Http\Requests\AutoComplete\AutoCompleteRequest;
use Illuminate\Support\Collection;

interface AutoCompleteService
{
    public function retrieveResults(AutoCompleteRequest $request): Collection;
}
