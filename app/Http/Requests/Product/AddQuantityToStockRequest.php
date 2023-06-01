<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AddQuantityToStockRequest extends FormRequest
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
            'productId' => [Rule::requiredIf(!$this->getExternalProductId()),'int','min:1'],
            'externalProductId' => [Rule::requiredIf(!$this->getProductId()), 'string'],
            'quantity' => ['required', 'int', 'min:1']
        ];
    }

    public function messages(): array
    {
        return [
            'productId.required' => 'O produto a ser adicionado é obrigatório.',
            'productId.int' => 'O ID do produto deve ser do tipo inteiro.',
            'productId.min' => 'O ID do produto deve ser maior que zero.',
            'externalProductId.required' => 'O código de identificação externo do produto é obrigatório.',
            'externalProductId.string' => 'O código de identificação externo do produto deve conter somente caracteres Alfa Numéricos.',
            'quantity.required' => 'A quantidade a ser adicionada é obrigatória.',
            'quantity.int' => 'A quantidade a ser adicionada deve ser composta de um número inteiro.',
            'quantity.min' => 'A quantidade a ser adicionada deve ser maior que zero.',
        ];
    }

    public function getProductId(): ?int
    {
        return $this->request->get('productId', null);
    }

    public function getExternalProductId(): ?string
    {
        return $this->request->get('externalProductId', null);
    }

    public function getQuantityToAdd(): int
    {
        return $this->request->get('quantity');
    }

    public function getAttributes(): Collection
    {
        return collect($this->validated());
    }
}
