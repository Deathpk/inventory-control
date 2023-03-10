<?php

namespace App\Http\Requests\Auth;

use App\Models\Plan;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'confirmed', 'email', 'unique:App\Models\User,email'],
            'roleId' => ['required', Rule::in(Role::getAvailableRoles())],
            'password' => ['required', 'confirmed',
                Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->uncompromised()
            ],
            'companyCnpj' => ['required','unique:App\Models\Company,cnpj'],
            'companyName' => ['required','string'],
            'planId' => ['required', Rule::in(Plan::getAvailablePlans())]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O campo nome deve conter somente caracteres alfa numéricos.',
            'name.min' => 'O campo nome deve ser composto de no minimo 3 caractéres',
            'name.max' => 'O campo nome deve ser composto de no máximo 120 caractéres',
            'email.required' => 'O campo e-mail é obrigatório',
            'email.email' => 'O e-mail inserido não é válido.',
            'email.confirmed' => 'o e-mail inserido e a confirmação não são os mesmos.',
            'email.unique' => 'O e-mail inserido já está sendo utilizado por outro usuário.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.confirmed' => 'Por favor , confirme a senha no campo confirmação.',
            'companyCnpj.required' => 'O CNPJ é obrigatório.',
            'companyName.string' => 'O campo nome da Companhia deve ser composto somente de caracteres alfa numéricos.',
            'companyCnpj.unique' => 'O CNPJ inserido já se encontra em uso por outra empresa. Peça ao administrador da empresa requerida para lhe convidar , caso seja funcionário da mesma.',
            'planId.required' => 'Por favor , escolha um plano antes de prosseguir.'
        ];
    }

    public function getAttributes(): Collection
    {
        return collect($this->validated());
    }
}
