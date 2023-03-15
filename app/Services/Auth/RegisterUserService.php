<?php

namespace App\Services\Auth;

use App\Http\Requests\Auth\RegisterApiTokenRequest;
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

    public function register(RegisterUserRequest $request): void
    {
        //TODO ADICIONAR O TRY CATCH DO AMOR E AMIZADE...
        $this->setAttributes($request);
        $this->createCompany();
        $this->createUser();
    }


    private function setAttributes(RegisterUserRequest|RegisterApiTokenRequest &$request): void
    {
        $this->attributes = $request->getAttributes();
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
            'email' => $this->attributes->get('email'),
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

    private function resolveAbilities(): array
    {
        //TODO
        return ['*'];
    }
}
