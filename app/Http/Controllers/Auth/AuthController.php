<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\Auth\FailedToIssueNewApiToken;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterApiTokenRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\Auth\RegisterApiTokenService;
use App\Services\Auth\RegisterUserService;
use App\Services\Auth\RevokeApiTokenService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{

    public function register(RegisterUserRequest $request, RegisterUserService $service)
    {
        //TODO
    }

    /**
     * @throws FailedToIssueNewApiToken
     */
    public function registerApiToken(RegisterApiTokenRequest $request, RegisterApiTokenService $service): JsonResponse
    {
        $token = $service->registerNewApiToken($request);
        return response()->json([
            'success' => true,
            'message' => 'Token de API criado com sucesso! , por favor , guarde com cuidado o token a seguir , e não o perca.',
            'token' => $token
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function revokeApiToken(int $tokenId, RevokeApiTokenService $service): JsonResponse
    {
        $service->revokeSelectedToken($tokenId);
        return response()->json([
            'success' => true,
            'message' => 'Token de API criado deletado com sucesso!',
        ]);
    }

    public function invite()
    {

    }

    public function login()
    {
        //TODO
    }

    public function logout()
    {
        //TODO
    }
}
