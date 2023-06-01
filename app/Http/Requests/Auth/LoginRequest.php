<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O e-mail é obrigatório',
            'email.email' => 'O e-mail inserido não é um e-mail válido. Por favor , tente novamente.',
            'password.required' => 'O campo senha é obrigatório'
        ];
    }

    public function getCredentials(): array
    {
        return [
            'email' => $this->request->get('email'),
            'password' => $this->request->get('password')
        ];
    }
}
