<?php

namespace App\Http\Controllers\Auth;

use App\Events\Auth\RecoverPasswordRequested;
use App\Exceptions\Auth\FailedToIssueNewApiToken;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangeUserPasswordRequest;
use App\Http\Requests\Auth\InviteEmployeeRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RecoverPasswordConfirmedRequest;
use App\Http\Requests\Auth\RecoverPasswordRequest;
use App\Http\Requests\Auth\RegisterApiTokenRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\Auth\ChangeUserPasswordService;
use App\Services\Auth\ConfirmPasswordRecoveryService;
use App\Services\Auth\InviteCompanyEmployeeService;
use App\Services\Auth\RegisterApiTokenService;
use App\Services\Auth\RegisterUserService;
use App\Services\Auth\RemoveOldUserTokenService;
use App\Services\Auth\RevokeApiTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuthController extends Controller
{

    public function getUserInfo(Request $request)
    {
        $user = Auth::user();
        return response()->json(
            $user->getGeneralInfo()
        );
    }

   public function inviteEmployee(InviteEmployeeRequest $request, InviteCompanyEmployeeService $service)
   {
       $service->invite($request);
       return response()->json([
            'success' => true,
            'message' => 'O Colaborador foi convidado com sucesso. Para continuar, ele deve acessar o e-mail que acaba de ser enviado para o endereço cadastrado.'
       ]);
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

    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->getCredentials())) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário e / ou senha inválidos.'
            ],401);
        }

        $user = Auth::user();

        $authToken = $user->createToken('testing')->plainTextToken;//$request->userAgent()

        return response()->json([
            'success' => true,
            'message' => 'Login efetuado com sucesso!',
            'token' => $authToken,
            'mustChangePassword' => Auth::user()->mustChangePassword(),
            'user' => $user->getGeneralInfo()
        ]);
    }

    public function logout(RemoveOldUserTokenService $service): JsonResponse
    {
        $service->removeOldTokens();
        return response()->json([
            'success' => true
        ]);
    }

    public function changePassword(ChangeUserPasswordRequest $request, ChangeUserPasswordService $service): JsonResponse
    {
        $service->changePassword($request);
        return response()->json([
            'success' => true,
            'message' => 'Senha alterada com sucesso, por favor, faça o login novamente.'
        ]);
    }

    public function recoverPassword(RecoverPasswordRequest $request): JsonResponse
    {
        event(new RecoverPasswordRequested($request->getEmail()));
        return response()->json([
            'success' => true
        ]);
    }

    public function confirmPasswordRecovery(RecoverPasswordConfirmedRequest $request, ConfirmPasswordRecoveryService $service): JsonResponse
    {
        $service->confirmPasswordRecovery($request->getRandomPassword());
        return response()->json([
            'success' => true
        ]);
    }
}
