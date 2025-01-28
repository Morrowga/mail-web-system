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
        $saRole = Role::where('name', '管理者')->first();

        if(empty($saRole)) {
            $saRole = Role::create([
                "name" => "管理者"
            ]);
        }

        $permissionArrays = [
            [
                "name" => "account_createdit",
                "display" => "アカウント登録・編集",
                "description" =>  "アカウントの登録・編集を許可します。"
            ],
            [
                "name" => "account_delete",
                "display" => "アカウント削除",
                "description" => "アカウントの削除を許可します。"
            ],
            [
                "name" => "account_read",
                "display" => "アカウント閲覧",
                "description" => "アカウント一覧の閲覧を許可します。"
            ],
            [
                "name" => "mail_reply",
                "display" => "メール返信",
                "description" => "メールの返信を許可します。"
            ],
            [
                "name" => "mail_forward",
                "display" => "メール転送",
                "description" => "メールの転送を許可します。"
            ],
            [
                "name" => "mail_delete",
                "display" => "メール削除",
                "description" => "メールの削除を許可します。"
            ],
            [
                "name" => "mail_read",
                "display" => "メール閲覧",
                "description" => "メールの閲覧を許可します。"
            ],
            [
                "name" => "mail_create",
                "display" => "メール作成・送信",
                "description" => "メールの作成・送信を許可します。"
            ],
            [
                "name" => "spam_createdit",
                "display" => "迷惑メール登録・編集",
                "description" => "迷惑メールの登録・編集を許可します。"
            ],
            [
                "name" => "spam_read",
                "display" => "迷惑メール閲覧",
                "description" => "迷惑メール一覧の閲覧を許可します。"
            ],
            [
                "name" => "spam_delete",
                "display" => "迷惑メール削除",
                "description" => "迷惑メール一覧からの削除を許可します。"
            ],
            [
                "name" => "folder_createdit",
                "display" => "フォルダ登録・編集",
                "description" => "フォルダの登録・編集を許可します。"
            ],
            [
                "name" => "folder_read",
                "display" => "フォルダ閲覧",
                "description" => "フォルダ一覧の閲覧を許可します。"
            ],
            [
                "name" => "folder_delete",
                "display" => "フォルダ削除",
                "description" => "フォルダの削除を許可します。"
            ],
            [
                "name" => "template_createdit",
                "display" => "テンプレート登録・編集",
                "description" => "テンプレートの登録・編集を許可します。"
            ],
            [
                "name" => "template_read",
                "display" => "テンプレート閲覧",
                "description" => "テンプレート一覧の閲覧を許可します。"
            ],
            [
                "name" => "template_delete",
                "display" => "テンプレート削除",
                "description" => "テンプレートの削除を許可します。"
            ],
            [
                "name" => "templatecategory_createdit",
                "display" => "テンプレートカテゴリ登録・編集",
                "description" => "テンプレートカテゴリの登録・編集を許可します。"
            ],
            [
                "name" => "templatecategory_read",
                "display" => "テンプレートカテゴリ閲覧",
                "description" => "テンプレートカテゴリ一覧の閲覧を許可します。"
            ],
            [
                "name" => "templatecategory_delete",
                "display" => "テンプレートカテゴリ削除",
                "description" => "テンプレートカテゴリの削除を許可します。"
            ],
            [
                "name" => "product_createdit",
                "display" => "Product Creation / Edit",
                "description" => "Product Creation and Modification"
            ],
            [
                "name" => "product_read",
                "display" => "Product Read",
                "description" => "Products"
            ],
            [
                "name" => "product_delete",
                "display" => "Product Deletion",
                "description" => "Product Deletion "
            ],
        ];

        foreach ($permissionArrays as $permissionArray) {
            if(Permission::where('name', $permissionArray['name'])->exists()) {
                continue;
            }

            Permission::create($permissionArray);
        }

        $permissions = Permission::pluck('id')->toArray();

        $saRole->syncPermissions($permissions);

        $user = User::where('id', 1)->first();

        $user->assignRole('管理者');

        // Assigned as SA ( Super Admin )

        $staffRole = Role::createOrFirst([
            "name" => "店員"
        ]);

        if(!User::where('login_id', 'staff')->exists()) {
            $createStaff = User::create([
                "name" => "staff",
                "email" => "staff@mail.com",
                "login_id" => 'staff',
                "password" => Hash::make('password'),
            ]);

            $createStaff->assignRole('店員');
        }

        $userRole = Role::createOrFirst([
            "name" => "User"
        ]);
    }
}
