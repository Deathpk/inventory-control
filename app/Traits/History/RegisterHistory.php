<?php

namespace App\Traits\History;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait RegisterHistory
{
    public static function getChangedBy(): int
    {
        if (Auth::user() instanceof User) {
            return Auth::user()->getCompany()->getId();
        }
        return Auth::user()->getId();
    }
}
