<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EmployeeInvited
{
    use Dispatchable, SerializesModels;

    protected User $invitedUser;
    protected string $randomPassword;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $invitedUser, string $randomPassword)
    {
        $this->invitedUser = $invitedUser;
        $this->randomPassword = $randomPassword;
    }

    public function getInvitedUser(): User
    {
        return $this->invitedUser;
    }

    public function getRandomPassword(): string
    {
        return $this->randomPassword;
    }
}
