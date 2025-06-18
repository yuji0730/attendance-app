<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 一般ユーザー
        User::create([
            'name' => '一般 太郎',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        User::create([
            'name' => '西伶奈',
            'email' => 'reina.n@coachtech.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        // 管理者
        User::create([
            'name' => '管理者 花子',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);
    }
}
