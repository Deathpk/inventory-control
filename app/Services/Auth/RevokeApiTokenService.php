<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\FailedToRevokeApiToken;
use App\Exceptions\Auth\TokenDoesNotExistOnCompany;
use App\Models\Company;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class RevokeApiTokenService
{
    private int $selectedTokenId;
    private Company|Authenticatable $loggedCompany;

    /**
     * @throws TokenDoesNotExistOnCompany|FailedToRevokeApiToken
     */
    public function revokeSelectedToken(int $tokenId): void
    {
        $this->setSelectedTokenId($tokenId);
        $this->setLoggedCompany();

        $tokenExists = $this->loggedCompany->specificTokenExists($tokenId);

        if (!$tokenExists) {
            throw new TokenDoesNotExistOnCompany();
        }

        try {
            $this->revokeToken();
        } catch (\Throwable $e) {
            throw new FailedToRevokeApiToken($e);
        }
    }

    private function setSelectedTokenId(int $tokenId): void
    {
        $this->selectedTokenId = $tokenId;
    }

    private function setLoggedCompany(): void
    {
        $this->loggedCompany = Auth::user();
    }

    private function revokeToken(): void
    {
        $this->loggedCompany->revokeSelectedToken($this->selectedTokenId);
    }
}
