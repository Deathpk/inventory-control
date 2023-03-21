<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreBrandRequest extends FormRequest
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
            'name' => 'required|string|max:80|min:3',
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
