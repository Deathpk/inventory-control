<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\Auth\RegisterUserService;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request, RegisterUserService $service)
    {
        //TODO ADICIONAR TRY CATCH DA AMIZADE!
        $service->registerNewUser($request);
        return response()->json([
            'success' => true,
            'message' => 'Usuário criado com sucesso!'
        ]);
    }

    public function invite()
    {

    }

    public function login()
    {

    }

    public function logout()
    {

    }
}
