<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RemoveProductFromBuyListRequest extends FormRequest
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
            'productId' => [Rule::requiredIf(!$this->getExternalProductId())],
            'externalProductId' => [Rule::requiredIf(!$this->getProductId())],
        ];
    }

    public function messages(): array
    {
        return [
            'productId.required' => 'O campo ID do produto é obrigatório!',
            'externalProductId.required' => 'O campo ExternalId do produto é obrigatório quando o ID do produto estiver ausente!',
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
