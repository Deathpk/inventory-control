<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateBuyListRequest extends FormRequest
{
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
            'productId' => [Rule::requiredIf(!$this->getExternalProductId()), 'int'],
            'externalProductId' => [Rule::requiredIf(!$this->getProductId()), 'string'],
            'repositionQuantity' => ['int', 'min:1']
        ];
    }

    public function messages(): array
    {
        return [
            'productId.required' => 'O campo ID do produto é obrigatório!',
            'productId.int' => 'O campo ID do produto deve conter somente números',
            'externalProductId.required' => 'O campo ExternalId do produto é obrigatório quando o ID do produto estiver ausente!',
            'externalProductId.string' => 'O campo ExternalId do produto deve conter somente caracteres alfa numéricos.',
            'repositionQuantity.int' => 'A quantidade para reposição deve conter um número inteiro',
            'repositionQuantity.min' => 'A quantidade para reposição deve ser de no minimo 1 produto.',
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
}
