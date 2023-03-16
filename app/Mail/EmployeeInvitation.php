<?php

namespace App\Mail;

use App\Models\User;

class EmployeeInvitation extends BaseEmail
{
    const SUBJECT = 'Você foi convidado para se juntar a sua equipe no Stock && Repo!';
    const VIEW = 'mail.employee-invitation-email';

    public function __construct(User $invitedUser, string $randomPassword)
    {
        $data = [
            'employeeName' => $invitedUser->name,
            'randomPassword' => $randomPassword,
            'loginLink' => env("FRONT_END_APP_URL")
        ];

        parent::__construct(
            self::SUBJECT,
            $invitedUser->email,
            $invitedUser->name,
            $data,
            self::VIEW
        );
    }
}
