<?php

namespace App\Services\Auth;

use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\Company;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Collection;

class RegisterUserService
{
    private Collection $attributes;
    private Company $createdCompany;
    private User $createdUser;

    public function registerNewUser(RegisterUserRequest $request)
    {
        //TODO ADICIONAR O TRY CATCH DO AMOR E AMIZADE...
        $this->setAttributes($request);

//        if ($this->shouldCheckForPlanPayment()) {
//            $this->validateIfUserPaidSelectedPlan();
//        }

        $this->createCompany();
        $this->createUser();
        $this->issueTokenForUser();
    }

    private function setAttributes(RegisterUserRequest &$request): void
    {
        $this->attributes = $request->getAttributes();
    }

    private function shouldCheckForPlanPayment(): bool
    {
        return ! ($this->attributes->get('planId') === Plan::FREE_PLAN);
    }

    private function validateIfUserPaidSelectedPlan(): bool
    {
        //TODO ADICIONAR VALIDAÇÃO , SE O USUÁRIO PAGOU COM SUCESSO O PLANO ESCOLHIDO. CASO NÃO , LANÇAR EXERCEÇÃO...
        return true;
    }

    private function createCompany(): void
    {
        $params = $this->getParamsForCompanyCreation();
        $this->createdCompany = Company::create()->fromArray($params);
    }

    private function getParamsForCompanyCreation(): array
    {
        return [
            'companyName' => $this->attributes->get('companyName'),
            'companyCnpj' => $this->attributes->get('companyCnpj'),
            'planId' => $this->attributes->get('planId')
        ];
    }

    private function createUser(): void
    {
        $params = $this->getParamsForUserCreation();
        $this->createdUser = User::create()->fromArray($params);
    }

    private function getParamsForUserCreation(): array
    {
        return [
            'name' => $this->attributes->get('name'),
            'email' => $this->attributes->get('email'),
            'password' => $this->attributes->get('password'),
            'roleId' => $this->attributes->get('roleId'),
            'companyId' => $this->createdCompany->getId()
        ];
    }

    private function issueTokenForUser(): void
    {
        $userAbilities = $this->resolveAbilities();
        dd($this->createdUser->createToken('user', $userAbilities)->toJson());
        $this->createdUser->createToken('user', $userAbilities);
    }

    private function resolveAbilities(): array
    {
        //TODO
        return ['*'];
    }

}
