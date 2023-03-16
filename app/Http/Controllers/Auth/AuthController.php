<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\Auth\FailedToIssueNewApiToken;
use App\Exceptions\Product\AttachmentInvalid;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangeUserPasswordRequest;
use App\Http\Requests\Auth\InviteEmployeeRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterApiTokenRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\Auth\ChangeUserPasswordService;
use App\Services\Auth\InviteCompanyEmployeeService;
use App\Services\Auth\RegisterApiTokenService;
use App\Services\Auth\RegisterUserService;
use App\Services\Auth\RemoveOldUserTokenService;
use App\Services\Auth\RevokeApiTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

   public function inviteEmployee(InviteEmployeeRequest $request, InviteCompanyEmployeeService $service)
   {
       $service->invite($request);
       return response()->json([
            'success' => true,
            'message' => 'Funcionário convidado com sucesso!'
       ], 200);
   }

    public function register(RegisterUserRequest $request, RegisterUserService $service): JsonResponse
    {
        $service->register($request);
        return response()->json([
            'success' => true,
            'message' => 'Cadastro concluído com sucesso , por favor , insira as informações de login e entre novamente.'
        ], 201);
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
        ], 200);
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

    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->getCredentials())) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário e / ou senha inválidos.'
            ],401);
        }

        $authToken = (Auth::user())->createToken('testing')->plainTextToken;//$request->userAgent()

        return response()->json([
            'success' => true,
            'message' => 'Login efetuado com sucesso!',
            'token' => $authToken,
            'mustChangePassword' => Auth::user()->mustChangePassword()
        ], 200);
    }

    public function logout(RemoveOldUserTokenService $service): JsonResponse
    {
        $service->removeOldTokens();
        return response()->json(['success' => true]);
    }

    public function changePassword(ChangeUserPasswordRequest $request, ChangeUserPasswordService $service): JsonResponse
    {
        $service->changePassword($request);
        return response()->json([
            'success' => true,
            'message' => 'Senha alterada com sucesso, por favor, faça o login novamente.'
        ], 200);
    }
}
