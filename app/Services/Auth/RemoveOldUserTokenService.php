<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\FailedToRevokeApiToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class RemoveOldUserTokenService
{
    public function removeOldTokens(): void
    {
        try {
            $user = Auth::user();
            $user->revokeLogedToken();
        } catch(\Throwable $e) {
            throw new FailedToRevokeApiToken($e);
        }
    }
}