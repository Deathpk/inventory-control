<?php

namespace App\Services\Auth;

use App\Models\History;
use App\Models\PasswordReset;
use App\Models\User;
use App\Services\History\HistoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ConfirmPasswordRecoveryService
{
    private $query;
    private $foundRequest;

    public function confirmPasswordRecovery(string $randomPassword): void
    {
        $this->query = PasswordReset::query()->where('token', $randomPassword);
        $this->foundRequest = $this->query->first();

        if(!empty($this->foundRequest)) {
            try {
                DB::beginTransaction();
                $this->processChange($randomPassword);
                DB::commit();
            } catch(Throwable $e) {
                DB::rollBack();
                Log::info("Message: {$e->getMessage()}\n Stacktrace: {$e->getTraceAsString()}");
            }
        }
    }

    private function processChange(string $randomPassword) : void
    {
        $user = User::where('email', $this->foundRequest->email)->first();
        if($user) {
            $user->changePassword($randomPassword, true);
            $this->registerPasswordRecoveryRequestHistory($user->id, $user->company_id);
            $this->query->delete();
            $user->revokeLogedToken();
        }
    }

    private function registerPasswordRecoveryRequestHistory(int $changedBy, int $companyId): void
    {
        History::create([
            'entity_id' => $changedBy,
            'entity_type' => History::USER_ENTITY,
            'metadata' => json_encode(['submitedFromIp' => $this->getIPAddress()]),
            'changed_by_id' => $changedBy,
            'company_id' => $companyId,
            'action_id' => History::USER_PASSWORD_CHANGED
        ]);
    }

    private function getIPAddress(): string 
    {  
        //whether ip is from the share internet  
         if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
                    $ip = $_SERVER['HTTP_CLIENT_IP'];  
        }  
        //whether ip is from the proxy  
        elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
        }  
        //whether ip is from the remote address  
        else {  
            $ip = $_SERVER['REMOTE_ADDR'];  
        }  
        return $ip;  
    }  
}