<?php

namespace App\Services\Auth;

use App\Events\EmployeeInvited;
use App\Exceptions\FailedToSendEmployeeInvitationEmail;
use App\Http\Requests\Auth\InviteEmployeeRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class InviteCompanyEmployeeService 
{
    /** 1. Criar um usuário com os dados recebidos e setar uma senha aleatória.
     *  2. Enviar um e-mail para confirmação e troca de senha para o usuário, contendo no corpo do e-mail a senha aleatória definida.
     * 
     */
    public function invite(InviteEmployeeRequest $request): void
    {
        $attributes = $request->getAttributes();
        $randomPassword = Str::random(9);
        $attributes->put('password', $randomPassword);
        $attributes->put('companyId', Auth::user()->company_id);
        $attributes->put('mustChangePassword', true);
        try {
            $invitedUser = User::create()->fromArray($attributes->toArray());
            Log::info($randomPassword);
            event(new EmployeeInvited($invitedUser, $randomPassword));
        } catch(Throwable $e) {
            throw new FailedToSendEmployeeInvitationEmail($e);
        }
    }
}