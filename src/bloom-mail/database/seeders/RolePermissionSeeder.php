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
            "name" => "Super Administrator"
        ]);

        $permissionArrays = [
            [
                "name" => "account_createdit",
                "display" => "Account Creation & Modification",
                "description" =>  "User can create the account and update the account info."
            ],
            [
                "name" => "account_delete",
                "display" => "Account Deletion",
                "description" => "User can delete the user accounts."
            ],
            [
                "name" => "account_read",
                "display" => "Account Readable",
                "description" => "User can read the list of user accounts."
            ],
            [
                "name" => "mail_reply",
                "display" => "Reply Mail",
                "description" => "User can reply the selected mail."
            ],
            [
                "name" => "mail_forward",
                "display" => "Forward Mail",
                "description" => "User can forward the selected mail."
            ],
            [
                "name" => "mail_delete",
                "display" => "Mail Deletion",
                "description" => "User can delete the selected mail."
            ],
            [
                "name" => "mail_read",
                "display" => "Mail Readable",
                "description" => "User can read the mail lists."
            ],
            [
                "name" => "mail_create",
                "display" => "Mail Creation",
                "description" => "User can send the mail."
            ],
            [
                "name" => "spam_createdit",
                "display" => "Spam Mail Registration & Modification",
                "description" => "User can register the spam mail."
            ],
            [
                "name" => "spam_read",
                "display" => "Spam Readable",
                "description" => "User can read the spam mail lists."
            ],
            [
                "name" => "spam_delete",
                "display" => "Spam Deletion",
                "description" => "User can delete the spam mail lists."
            ],
            [
                "name" => "folder_createdit",
                "display" => "Folder Creation & Modification",
                "description" => "User can create and edit the folder."
            ],
            [
                "name" => "folder_read",
                "display" => "Folder Readable",
                "description" => "User can read the folder lists."
            ],
            [
                "name" => "folder_delete",
                "display" => "Folder Deletion",
                "description" => "User can delete the folder lists."
            ],
            [
                "name" => "template_createdit",
                "display" => "Template Creation & Modification",
                "description" => "User can create and edit the template."
            ],
            [
                "name" => "template_read",
                "display" => "Template Readable",
                "description" => "User can read the template lists."
            ],
            [
                "name" => "template_delete",
                "display" => "Template Deletion",
                "description" => "User can delete the template lists."
            ],
            [
                "name" => "templatecategory_createdit",
                "display" => "Template Category Creation & Modification",
                "description" => "User can create and edit the template category."
            ],
            [
                "name" => "templatecategory_read",
                "display" => "Template Category Readable",
                "description" => "User can read the template category lists."
            ],
            [
                "name" => "templatecategory_delete",
                "display" => "Template Category Deletion",
                "description" => "User can delete the template category lists."
            ],
        ];

        foreach ($permissionArrays as $permissionArray) {
            Permission::create($permissionArray);
        }

        $permissions = Permission::pluck('id')->toArray();

        $saRole->syncPermissions($permissions);

        $user = User::where('id', 1)->first();

        $user->assignRole('Super Administrator');

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
