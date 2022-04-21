<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class RemoveSoldUnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'soldQuantity' => 'required|int',
            'productId' => 'required|int'
        ];
    }

    public function messages(): array
    {
        return [
            'soldQuantity.required' => 'A quantidade de unidades vendidas é obrigatória.',
            'soldQuantity.int' => 'A quantidade de unidades vendidas deve conter somente numerais inteiros.',
            'productId.required' => 'O ID do produto é obrigatório.',
            'productId.int' => 'O ID do produto deve conter somente numerais inteiros.',
        ];
    }

    public function getAttributes(): array
    {
        return $this->validated();
    }
}
