<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
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
            'productId' => 'required',
            'isExternal' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'productId.required' => 'O campo Id do produto é obrigatório!',
            'isExternal.required' => 'O campo isExternal é obrigatório!',
        ];
    }

    public function getProductId(): int|string
    {
        if($this->getIsExternal()) {
            return $this->query('productId');
        }

        return (int) $this->query('productId');
    }

    public function getIsExternal(): bool
    {
        return (bool) $this->query('isExternal');
    }

    public function getAttributes(): Collection
    {
        return collect($this->validated());
    }
}
