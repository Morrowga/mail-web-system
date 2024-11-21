<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Interfaces\AuthRepositoryInterface;
use App\Http\Requests\AccountCreationRequest;

class AuthController extends Controller
{
    private AuthRepositoryInterface $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function indexAccountRegistration()
    {
        return Inertia::render('Profile/Create');
    }

    public function accountRegistration(AccountCreationRequest $request)
    {
        $this->authRepository->accountRegistration($request);

        return redirect()->route('profile.account');
    }
}
