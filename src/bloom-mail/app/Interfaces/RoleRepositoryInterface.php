<?php

namespace App\Interfaces;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

interface RoleRepositoryInterface
{
    public function index();

    public function store(Request $request);

    public function update(Request $request, Role $role);

    public function delete(Role $role);
}
