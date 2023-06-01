<?php

namespace App\Http\Requests\AutoComplete;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AutoCompleteRequest extends FormRequest
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
            'input' => ['required', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'input.required' => 'O campo input é obrigatório.',
            'input.string' => 'O campo input deve conter somente caracteres alfa numéricos.'
        ];
    }

    public function getInput(): string
    {
        return $this->query('input');
    }
}
