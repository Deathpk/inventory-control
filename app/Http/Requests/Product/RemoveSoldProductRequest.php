<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

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
            'soldQuantity' => 'required|int',
            'productId' => [Rule::requiredIf(!$this->getExternalProductId()), 'int'],
            'externalProductId' => [Rule::requiredIf(!$this->getProductId()), 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'soldQuantity.required' => 'A quantidade de unidades vendidas é obrigatória.',
            'soldQuantity.int' => 'A quantidade de unidades vendidas deve conter somente numerais inteiros.',
            'productId.required' => 'O ID do produto é obrigatório.',
            'productId.int' => 'O ID do produto deve conter somente numerais inteiros.',
            'externalProductId.required' => 'O código de identificação externo do produto é obrigatório.',
            'externalProductId.string' => 'O código de identificação externo do produto deve conter somente caracteres Alfa Numéricos.',
        ];
    }

    public function getAttributes(): array
    {
        return $this->validated();
    }

    public function getProductId(): ?int
    {
        return $this->request->get('productId', null);
    }

    public function getExternalProductId(): ?string
    {
        return $this->request->get('externalProductId', null);
    }
}
