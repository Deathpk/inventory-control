<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ChangeUserPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'oldPassword' => ['required'],
            'newPassword' => ['required', 'confirmed',
                Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->uncompromised()
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'oldPassword.required' => 'O campo senha antiga é obrigatório!',
            'newPassword.required' => 'O campo nova senha é obrigatório!',
            'newPassword.confirmed' => 'O campo nova senha e sua confirmação devem ser idênticos!'
        ];
    }

    public function getAttributes(): Collection
    {
        return collect($this->validated());
    }
}
