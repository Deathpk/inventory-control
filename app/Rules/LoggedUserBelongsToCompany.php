<?php

namespace App\Rules;

use App\Models\Company;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use function Pest\Laravel\instance;

class LoggedUserBelongsToCompany implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $loggedUser = Auth::user();
        $companyId = $loggedUser instanceof Company ? $loggedUser->getId() : $loggedUser->getCompany()->getId();

        $isUserPartOfRequestedCompany = $companyId === $value;

        if (!$isUserPartOfRequestedCompany) {
            Log::warning("O usuário logado não faz parte da companhia requisitada. \n Usuário: {$loggedUser->getId()} \n Companhia do usuário logado: {$companyId}.");
        }

        return $isUserPartOfRequestedCompany;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O usuário logado não pertence a companhia escolhida.';
    }
}
