<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assigned as SA ( Super Admin )

        $saRole = Role::create([
            "name" => "SA"
        ]);

        $permissionArrays = [
            ["name" => "account_createdit"],
            ["name" => "account_delete"],
            ["name" => "account_read"],
            ["name" => "mail_reply"],
            ["name" => "mail_forward"],
            ["name" => "mail_delete"],
            ["name" => "mail_read"],
            ["name" => "mail_create"]
        ];

        foreach ($permissionArrays as $permissionArray) {
            Permission::create($permissionArray);
        }

        $permissions = Permission::pluck('id')->toArray();

        $saRole->syncPermissions($permissions);

        $user = User::where('id', 1)->first();

        $user->assignRole('SA');

        // Assigned as SA ( Super Admin )

        $staffRole = Role::create([
            "name" => "Staff"
        ]);

        $createStaff = User::create([
            "name" => "staff",
            "email" => "staff@mail.com",
            "login_id" => 'staff',
            "password" => Hash::make('password'),
        ]);

        $createStaff->assignRole('Staff');

    }
}
