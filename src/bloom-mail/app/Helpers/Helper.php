<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('check_user_permission')) {
    /**
     * Check if the current authenticated user has the given permission.
     *
     * @param string $permission The incoming permission to check.
     * @return bool True if the user has the permission, false otherwise.
     */
    function check_user_permission(string $permission): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

        return in_array($permission, $userPermissions);
    }
}


if (!function_exists('check_superadmin')) {
    /**
     * Check if the current authenticated user has the given permission.
     * @return bool True if the user has the permission, false otherwise.
     */
    function check_superadmin(): bool
    {
        $userRole = Auth::user()->getRoleNames()->first();

        return $userRole == '管理者' ? true : false;
    }
}
