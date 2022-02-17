<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

class UpdateProductRequest extends FormRequest
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
            'productId' => 'required|int',
            'attributes' => 'required|array'
        ];
    }

    public function messages()
    {
        return [
            'productId.required' => 'O id do produto é obrigatório.',
            'productId.int' => 'O id do produto deve conter somente numerais.',
            'attributes.required' => 'Antes de salvar , faça ao menos uma alteração no produto.',
            'attributes.array' => 'O campo atributo deve ser do tipo array.',
        ];
    }

    public function getProductId(): int
    {
        return $this->request->get('productId');
    }

    public function getAttributes(): Collection
    {
        return collect($this->request->get('attributes'));
    }
}
