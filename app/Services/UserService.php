<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function seedAdminUser()
    {
        $adminEmail = 'admin@gmail.com';
        $admin = DB::table('users')->where('email', $adminEmail)->first();

        if (!$admin) {
            DB::table('users')->insert([
                'fullname' => 'Quản trị viên',
                'email' => $adminEmail,
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'created_at' => now()
            ]);
        }
    }
}
