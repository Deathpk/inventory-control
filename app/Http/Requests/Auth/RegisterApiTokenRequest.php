<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class RegisterApiTokenRequest extends FormRequest
{
    const ALIAS_MIN_CHARACTERS = 3;
    const ALIAS_MAX_CHARACTERS = 80;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $loggedEntity = Auth::user();

        return Auth::check() &&
            $loggedEntity instanceof User &&
            $loggedEntity->getCompany()->canGenerateApiToken();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'alias' => [
                'required',
                'string',
                'min:'.self::ALIAS_MIN_CHARACTERS,
                'max:'.self::ALIAS_MAX_CHARACTERS
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'alias.required' => 'O campo alias do token é obrigatório.',
            'alias.string' => 'O campo alias do token deve ser composto somente por caractéres alfa numéricos.',
            'alias.min' => 'O campo alias do token deve conter no mínimo '. self::ALIAS_MIN_CHARACTERS .'caractéres.',
            'alias.max' => 'O campo alias do token deve conter no máximo '. self::ALIAS_MAX_CHARACTERS .'caractéres.',
        ];
    }

    public function getTokenAlias(): string
    {
        return collect($this->validated())->get('alias');
    }
}
