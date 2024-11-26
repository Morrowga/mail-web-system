<?php

namespace App\Interfaces;

use App\Models\User;
use Illuminate\Http\Request;

interface UserRepositoryInterface
{
    public function index();

    public function store(Request $request);

    public function update(Request $request, User $user);

    public function delete(User $user);
}
