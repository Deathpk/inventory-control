<?php

namespace App\Http\Requests\Employee;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role_id === Role::ADMIN_ROLE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => ['required'],
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'roleId' => ['required', Rule::in(Role::getAvailableRolesId())],
            'email' => ['required', 'email', 'unique:App\Models\User,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O campo nome deve conter somente caracteres alfa numéricos.',
            'name.min' => 'O campo nome deve ser composto de no minimo 3 caractéres',
            'name.max' => 'O campo nome deve ser composto de no máximo 120 caractéres',
            'role.required' => 'O cargod o colaborador é obrigatório.',
            'email.required' => 'O e-mail do colaborador é obrigatório.',
            'email.email' => 'O e-mail do colaborador deve ser um e-mail válido.',
            'email.unique' => 'Um colaborador Já utiliza o e-mail inserido.'
        ];
    }

    public function getAttributes(): Collection
    {
        return collect($this->validated());
    }
}
