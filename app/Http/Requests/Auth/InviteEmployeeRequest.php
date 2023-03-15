<?php

namespace App\Http\Requests\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InviteEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $loggedEntity = Auth::user();
        return get_class($loggedEntity) === User::class && $loggedEntity->getRoleId() === Role::ADMIN_ROLE;
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
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O campo nome deve conter somente caracteres alfa numéricos.',
            'name.min' => 'O campo nome deve ser composto de no minimo 3 caractéres',
            'name.max' => 'O campo nome deve ser composto de no máximo 120 caractéres'
        ];
    }

    public function getAttributes(): Collection
    {
        return collect($this->validated());
    }
}
