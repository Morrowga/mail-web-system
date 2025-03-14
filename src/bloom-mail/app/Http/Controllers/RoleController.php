<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use App\Interfaces\RoleRepositoryInterface;

class RoleController extends Controller
{
    private RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!check_superadmin()) {
            return abort(401);
        }

        $roles = $this->roleRepository->index();

        return Inertia::render('System/Roles/Index', [
            "roles" => $roles['data']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!check_superadmin()) {
            return abort(401);
        }

        $permissions = Permission::select('name', 'id', 'display')->get();

        return Inertia::render('System/Roles/CreateEdit', [
            "permissions" => $permissions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        if (!check_superadmin()) {
            return abort(401);
        }

        $createRole = $this->roleRepository->store($request);

        return redirect()->route('roles.index')->with('success', 'Form submitted successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        if (!check_superadmin()) {
            return abort(401);
        }

        $permissions = Permission::select('name', 'display', 'id')->get();

        return Inertia::render('System/Roles/CreateEdit', [
            "role" => $role,
            "role_permissions" => $role->permissions()->pluck('id'),
            "permissions" => $permissions
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, Role $role)
    {
        if (!check_superadmin()) {
            return abort(401);
        }

        $updateRole = $this->roleRepository->update($request, $role);

        return redirect()->route('roles.index')->with('success', 'Form submitted successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if (!check_superadmin()) {
            return abort(401);
        }

        $deleteRole = $this->roleRepository->delete($role);

        return redirect()->back();
    }
}
