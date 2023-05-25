<?php

namespace App\Policies;

use App\Models\Plan;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class FinancialModulePolicy
{
    use HandlesAuthorization;

    public static function accessFinancialModule(): bool
    {
        $loggedUser = Auth::user();
        return $loggedUser->getCompany()->plan_id === Plan::PREMIUM_PLAN 
            && $loggedUser->role_id === Role::ADMIN_ROLE;
    }
}
