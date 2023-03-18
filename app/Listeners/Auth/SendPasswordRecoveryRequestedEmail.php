<?php

namespace App\Listeners\Auth;

use App\Events\Auth\RecoverPasswordRequested;
use App\Mail\PasswordRecoveryRequested;
use App\Models\PasswordReset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendPasswordRecoveryRequestedEmail implements ShouldQueue
{
    use InteractsWithQueue;
    private string $requestedEmail;
    private string $randomPassword;

    public function handle(RecoverPasswordRequested $event): void
    {
        $this->requestedEmail = $event->getEmail();
        $this->randomPassword = Str::random(9);

        $emailExists = !empty(User::query()->where('email', $this->requestedEmail)->first('email'));

        if($emailExists) {
            $this->createPasswordResetRequest();
            Mail::send(new PasswordRecoveryRequested($this->requestedEmail, $this->randomPassword));
        }
    }

    private function createPasswordResetRequest() {
        PasswordReset::create($this->requestedEmail, $this->randomPassword);
    }
}
