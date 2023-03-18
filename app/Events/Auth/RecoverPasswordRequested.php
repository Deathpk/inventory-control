<?php

namespace App\Events\Auth;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RecoverPasswordRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private string $recoveryEmail;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $email)
    {
        $this->recoveryEmail = $email;
    }

    public function getEmail(): string
    {
        return $this->recoveryEmail;
    }
}
