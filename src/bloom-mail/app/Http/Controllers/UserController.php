<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Requests\AccountUpdateRequest;
use App\Interfaces\UserRepositoryInterface;
use App\Http\Requests\AccountCreationRequest;

class UserController extends Controller
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $users = $this->userRepository->index();

        return Inertia::render('System/Users/Index', [
            "users" => $users['data']
        ]);
    }

    public function create()
    {

        return Inertia::render('System/Users/CreateEdit',[
            "roles" => Role::get()
        ]);
    }

    public function store(AccountCreationRequest $request)
    {
        $this->userRepository->store($request);

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        return Inertia::render('System/Users/CreateEdit', [
            "user" => $user,
            "roles" => Role::get()
        ]);
    }

    public function update(AccountUpdateRequest $request, User $user)
    {
        $updateUser = $this->userRepository->update($request, $user);

        return redirect()->route('users.index')->with('success', 'Form submitted successfully');
    }

    /**
        * Remove the specified resource from storage.
        */
    public function destroy(User $user)
    {
        $deleteUser = $this->userRepository->delete($user);

        return redirect()->back();
    }
}
