<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\Auth\FailedToIssueNewApiToken;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterApiTokenRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\Auth\RegisterApiTokenService;
use App\Services\Auth\RegisterUserService;
use App\Services\Auth\RevokeApiTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function register(RegisterUserRequest $request, RegisterUserService $service): JsonResponse
    {
        $service->register($request);
        return response()->json([
            'success' => true,
            'message' => 'Cadastro concluído com sucesso , por favor , insira as informações de login e entre novamente.'
        ]);
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
        //TODO
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->getCredentials())) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário e / ou senha inválidos.'
            ],401);
        }

        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Login efetuado com sucesso!'
        ]);
    }

    public function logout()
    {
        //TODO
    }
}
