<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class AddQuantityToStockRequest extends FormRequest
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
    public function rules()
    {
        return [
            'productId' => 'required|int|min:1',
            'quantity' => 'required|int|min:1'
        ];
    }

    public function messages()
    {
        return [
            'productId.required' => 'O produto a ser adicionado é obrigatório.',
            'productId.int' => 'O ID do produto deve ser do tipo inteiro.',
            'productId.min' => 'O ID do produto deve ser maior que zero.',
            'quantity.required' => 'A quantidade a ser adicionada é obrigatória.',
            'quantity.int' => 'A quantidade a ser adicionada deve ser composta de um número inteiro.',
            'quantity.min' => 'A quantidade a ser adicionada deve ser maior que zero.',
        ];
    }

    public function getProductId(): int
    {
        return $this->request->get('productId');
    }

    public function getQuantityToAdd(): int
    {
        return $this->request->get('quantity');
    }

}
