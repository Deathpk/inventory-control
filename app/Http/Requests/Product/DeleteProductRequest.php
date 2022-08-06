<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DeleteProductRequest extends FormRequest
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
            'productId' => 'required',
            'isExternal' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
          'productId.required' => 'O campo ID do produto é obrigatório.',
          'isExternal.required' => 'O campo IsExternal é obrigatório.'
        ];
    }

    public function getAttributes(): Collection
    {
        return collect($this->validated());
    }
}
