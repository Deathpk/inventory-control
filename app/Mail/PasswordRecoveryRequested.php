<?php

namespace App\Mail;

class PasswordRecoveryRequested extends BaseEmail
{
    const SUBJECT = 'Uma recuperação de senha foi solicitada!';
    const VIEW = 'mail.password-recovery-requested-email';

    public function __construct(string $recoveryEmail, string $randomPassword)
    {
        $data = [
            'randomPassword' => $randomPassword,
        ];

        parent::__construct(
            self::SUBJECT,
            $recoveryEmail,
            'Cliente Stock && Repo',
            $data,
            self::VIEW
        );
    }
}
