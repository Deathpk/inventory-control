<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RecoverPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
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
            'recoveryEmail' => 'required|email'
        ];
    }

    public function messages(): array
    {
        return[
            'recoveryEmail.required' => 'O campo e-mail de recuperação é obrigatório!',
            'recoveryEmail.email' => 'O campo e-mail de recuperação deve conter um e-mail válido!'
        ];
    }

    public function getEmail(): string
    {
        return $this->validated()['recoveryEmail'];
    }
}
