<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Requests\PermissionRequest;
use Spatie\Permission\Models\Permission;
use App\Interfaces\PermissionRepositoryInterface;

class PermissionController extends Controller
{
    private PermissionRepositoryInterface $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!check_superadmin()) {
            return abort(401);
        }

        $permissions = $this->permissionRepository->index();

        return Inertia::render('System/Permissions/Index', [
            "permissions" => $permissions['data']
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

        return Inertia::render('System/Permissions/CreateEdit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PermissionRequest $request)
    {
        if (!check_superadmin()) {
            return abort(401);
        }

        $createPermission = $this->permissionRepository->store($request);

        return redirect()->route('permissions.index')->with('success', 'Form submitted successfully');
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
    public function edit(Permission $permission)
    {
        if (!check_superadmin()) {
            return abort(401);
        }

        return Inertia::render('System/Permissions/CreateEdit', [
            "permission" => $permission
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PermissionRequest $request, Permission $permission)
    {
        if (!check_superadmin()) {
            return abort(401);
        }

        $updatePermission = $this->permissionRepository->update($request, $permission);

        return redirect()->route('permissions.index')->with('success', 'Form submitted successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        if (!check_superadmin()) {
            return abort(401);
        }

        $deletePermission = $this->permissionRepository->delete($permission);

        return redirect()->back();
    }
}
