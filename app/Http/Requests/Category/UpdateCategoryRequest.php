<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // TODO MONTAR A LOGICA DE AUTH DPS...
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
            'description' => 'string'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O campo nome da categoria é obrigatório.',
            'name.string' => 'O campo nome deve conter somente caracteres alfanuméricos.',
            'description.stirng' => 'O campo descrição deve conter somente caracteres alfanuméricos.'
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

    public function getAttributes(): Collection
    {
        return collect($this->validated());
    }
}
