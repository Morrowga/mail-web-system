<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\LoginRequest;
use App\Http\Requests\API\RegistrationRequest;
use App\Interfaces\API\AuthRepositoryInterface;

class AuthController extends Controller
{
    private AuthRepositoryInterface $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function registration(RegistrationRequest $request)
    {
        $registration = $this->authRepository->registration($request);

        return $registration;
    }

    public function login(LoginRequest $request)
    {
        $login = $this->authRepository->login($request);

        return $login;
    }
}
