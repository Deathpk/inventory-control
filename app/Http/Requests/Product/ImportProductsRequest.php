<?php

namespace App\Http\Requests\Product;

use App\Models\Plan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ImportProductsRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->getCompany()->plan_id === Plan::PREMIUM_PLAN;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => ['required','file', 'mimes:xlsx,xls,csv'],
        ];
    }

    public function messages()
    {
        return [
          'file.required' => 'Upload de arquivo obrigatório.',
          'file.file' => 'O arquivo está corrompido , ou é invalido , tente novamente.',
          'file.mimes' => 'O arquivo deve ser um arquivo válido com extensão xlsx (Excel) ou csv.'
        ];
    }

    public function getImportedFile()
    {
        return $this->validated('file');
    }
}
