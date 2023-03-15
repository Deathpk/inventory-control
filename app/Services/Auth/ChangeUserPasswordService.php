<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\FailedToChangeUserPassword;
use App\Exceptions\Auth\OldPasswordInvalid;
use App\Http\Requests\Auth\ChangeUserPasswordRequest;
use App\Models\History;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Services\History\HistoryService;
use Throwable;

class ChangeUserPasswordService
{
    public function changePassword(ChangeUserPasswordRequest $request): void
    {
        $loggedUser = Auth::user();
        $attributes = $request->getAttributes();
        $passwordIsCorrect = Hash::check($attributes->get('oldPassword'), $loggedUser->password);

        if(!$passwordIsCorrect) {
            throw new OldPasswordInvalid();
        }

        try {
            $loggedUser->changePassword($attributes->get('newPassword'));
            $this->registerPasswordChangedHistory($loggedUser->id);
            $loggedUser->revokeLogedToken();
            DB::commit();
        } catch(Throwable $e) {
            DB::rollback();
            throw new FailedToChangeUserPassword($e);
        }
    }

    private function registerPasswordChangedHistory(int $changedBy): void
    {
        $historyService = new HistoryService();

        $params =  [
            'entityId' => $changedBy,
            'entityType' => History::USER_ENTITY,
            'changedById' => $changedBy,
        ];

        $historyService->createHistory(History::USER_PASSWORD_CHANGED, $params);
    }
}