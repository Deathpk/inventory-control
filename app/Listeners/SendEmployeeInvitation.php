<?php

namespace App\Listeners;

use App\Events\EmployeeInvited;
use App\Mail\EmployeeInvitation;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmployeeInvitation
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Events\EmployeeInvited  $event
     * @return void
     */
    public function handle(EmployeeInvited $event): void
    {
        $invitedUser = $event->getInvitedUser();
        $randomPassword = $event->getRandomPassword();
        Mail::send(new EmployeeInvitation($invitedUser, $randomPassword));
    }
}
