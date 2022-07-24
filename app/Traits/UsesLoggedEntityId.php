<?php

namespace App\Traits;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait UsesLoggedEntityId
{
    public static function getLoggedCompanyId(): int
    {
        return Auth::user() instanceof User
            ? Auth::user()->getCompany()->getId()
            : Auth::user()->getId();
    }

    public static function getLoggedEntityInstance(): User|Company
    {
        /** @var User|Company $loggedEntityInstance */
        $loggedEntityInstance = Auth::user();
        return $loggedEntityInstance;
    }
}
