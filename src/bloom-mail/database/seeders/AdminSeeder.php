<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $find = User::where('email', 'admin@mail.com')->first();

        if(empty($find))
        {
            $user = User::create([
                "name" => "admin",
                "email" => "admin@mail.com",
                "login_id" => 'admin',
                "password" => Hash::make('password'),
            ]);
        }

    }
}
