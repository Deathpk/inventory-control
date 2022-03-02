<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:80|min:3'
        ];
    }

    public function messages()
    {
        return[
            'name.required' => 'O nome da marca é obrigatório.',
            'name.string' => 'O nome da marca deve conter somente caracteres alfa numéricos.',
            'name.max' => 'O nome da marca deve conter no máximo 80 caracteres.',
            'name.min' => 'O nome da marca deve conter no minimo 3 caracteres.',
        ];
    }

    public function getName(): string
    {
        return $this->request->get('name');
    }
}
