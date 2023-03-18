<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RecoverPasswordConfirmedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->request->get('randomPassword') !== null || $this->request()->get('randomPassword') !== '';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'randomPassword' => 'required'
        ];
    }

    public function messages(): array
    {
        return[
            'randomPassword.required' => 'O código único é obrigatório.',
        ];
    }

    public function getRandomPassword(): string
    {
        return $this->validated()['randomPassword'];
    }
}
