<?php

namespace App\Interfaces;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

interface PermissionRepositoryInterface
{
    public function index();

    public function store(Request $request);

    public function update(Request $request, Permission $permission);

    public function delete(Permission $permission);
}
