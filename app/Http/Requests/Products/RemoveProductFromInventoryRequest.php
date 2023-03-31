<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class RemoveProductFromInventoryRequest extends FormRequest
{
        /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'products.*' => ['required', 'array'],
            'products.*.quantityToRemove' => ['required', 'int']
        ];
    }

    public function messages(): array
    {
        return [
            'products.required' => 'É necessário ter ao menos um produto para dar baixa em estoque.',
            'products.array' => 'Os produtos devem estar contidos em um array de objetos.',
            'products.productId.required' => 'O ID de cada produto é obrigatório.',
            'products.quantityToRemove.required' => 'A quantidade de unidades para baixa em estoque é obrigatóra em cada produto.',
            'products.quantityToRemove.int' => 'A quantidade de unidades para baixa deve conter somente numerais inteiros.',
        ];
    }

    public function getAttributes(): Collection
    {
        return collect($this->toArray()['products']);
    }
}
