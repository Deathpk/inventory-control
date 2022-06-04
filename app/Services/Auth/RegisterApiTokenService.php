<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\FailedToIssueNewApiToken;
use App\Http\Requests\Auth\RegisterApiTokenRequest;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class RegisterApiTokenService
{
    private string $tokenAlias;

    /**
     * @throws FailedToIssueNewApiToken
     */
    public function registerNewApiToken(RegisterApiTokenRequest $request): string
    {
        $this->setTokenAlias($request);

        try {
            return $this->issueTokenForCompany();
        } catch (\Throwable $e) {
            throw new FailedToIssueNewApiToken($e);
        }
    }

    private function setTokenAlias(RegisterApiTokenRequest $request): void
    {
        $this->tokenAlias = $request->getTokenAlias();
    }

    private function issueTokenForCompany(): string
    {
        /** @var Company $company */
        $company = Auth::user()->getCompany();
        return $company->createToken($this->tokenAlias)->plainTextToken;
    }
}
