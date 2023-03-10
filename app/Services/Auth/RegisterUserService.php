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
        $this->resolvePaymentIfRequired();
        $this->createCompany();
        $this->createUser();
    }


    private function setAttributes(RegisterUserRequest|RegisterApiTokenRequest &$request): void
    {
        $this->attributes = $request->getAttributes();
    }

    private function resolvePaymentIfRequired(): void
    {
        if ($this->shouldValidatePlanPayment()) {
             $this->validatePayment();
        }
    }

    private function shouldValidatePlanPayment(): bool
    {
        //TODO REFATORAR ISSO DAQUI, COMO NÃO IREMOS MAIS TER UM PLANO FREE, PODEMOS VERIFICAR OUTRA COISA.
        // POR EXEMPLO, SE O PAGAMENTO ESTÁ EM DIA. PARA ISSO PRECISAREMOS CHECAR NA TABELA DE ALGUMA FORMA.
        //return ! ($this->attributes->get('planId') === Plan::FREE_PLAN);
        return true;
    }

    private function validatePayment(): void
    {
        //TODO ADICIONAR VALIDAÇÃO , SE O USUÁRIO PAGOU COM SUCESSO O PLANO ESCOLHIDO. CASO NÃO , LANÇAR EXERCEÇÃO...
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
