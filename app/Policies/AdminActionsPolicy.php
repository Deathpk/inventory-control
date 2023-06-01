<?php

namespace App\Policies;

use App\Exceptions\DoesNotComplyWithPolicy;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class AdminActionsPolicy
{
    use HandlesAuthorization;

    public function ensureUserCanPerformAdminActions(): void
    {
        $canProceed = Auth::user()->role_id ===Role::ADMIN_ROLE;
        if(!$canProceed) {
            throw new DoesNotComplyWithPolicy('Essa ação só pode ser executada por administradores.');
        }
    }
}
