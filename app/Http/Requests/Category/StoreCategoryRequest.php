<?php

namespace App\Http\Requests\Category;

use App\Rules\LoggedUserBelongsToCompany;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
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
    public function rules()
    {
        return [
            'name' => 'required|string',
            'description' => 'string',
            'company_id' => ['required', 'int', new LoggedUserBelongsToCompany]
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O campo nome da categoria é obrigatório.',
            'name.string' => 'O campo nome deve conter somente caracteres alfanuméricos.',
            'description.string' => 'O campo descrição deve conter somente caracteres alfanuméricos.',
            'company_id.required' => 'O campo Companhia é obrigatório..',
            'company_id.int' => 'O campo Companhia deve ser composto somente por numerais.',
        ];
    }

    public function getName(): string
    {
        return $this->request->get('name');
    }

    public function getDescription(): ?string
    {
        return $this->request->get('description');
    }

    public function getCompanyId(): ?int
    {
        return $this->request->get('company_id');
    }

    public function getAttributes(): Collection
    {
        return collect($this->validated());
    }
}
