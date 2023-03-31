<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class RemoveSoldProductRequest extends FormRequest
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
            'soldProducts.*' => ['required', 'array'],
//            'soldProducts.*.productId' => [Rule::requiredIf(!$this->externalProductIdExists()), 'int'],
            'soldProducts.*.soldQuantity' => ['required', 'int'],
//            'soldProducts.externalProductId' => [Rule::requiredIf(!$this->getProductId()), 'string']
//            'soldQuantity' => 'required|int',
//            'productId' => [Rule::requiredIf(!$this->getExternalProductId()), 'int'],
//            'externalProductId' => [Rule::requiredIf(!$this->getProductId()), 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'soldProducts.required' => 'Os produtos vendidos são obrigatórios.',
            'soldProducts.array' => 'Os produtos vendidos devem estar contidos em um array de objetos.',
            'soldProducts.productId.required' => 'O ID de cada produto vendido é obrigatório.',
            'soldProducts.soldQuantity.required' => 'A quantidade de unidades vendidas é obrigatória em cada produto vendido.',
            'soldProducts.soldQuantity.int' => 'A quantidade de unidades vendidas deve conter somente numerais inteiros.',
//            'soldQuantity.required' => 'A quantidade de unidades vendidas é obrigatória.',
//            'soldQuantity.int' => 'A quantidade de unidades vendidas deve conter somente numerais inteiros.',
//            'productId.required' => 'O ID do produto é obrigatório.',
//            'productId.int' => 'O ID do produto deve conter somente numerais inteiros.',
//            'externalProductId.required' => 'O código de identificação externo do produto é obrigatório.',
//            'externalProductId.string' => 'O código de identificação externo do produto deve conter somente caracteres Alfa Numéricos.',
        ];
    }

    public function getAttributes(): Collection
    {
        return collect($this->toArray()['soldProducts']);
    }
}
