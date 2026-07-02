<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Attempt login. Returns user array on success, or null on failure.
     */
    public function attemptLogin(string $email, string $password): ?array
    {
        if ($email === '' || $password === '') {
            return null;
        }

        $user = DB::table('users')
            ->leftJoin('user_info', 'user_info.user_id', '=', 'users.id')
            ->select(
                'users.id',
                DB::raw('COALESCE(user_info.fullname, users.fullname) AS fullname'),
                'users.password',
                'users.role',
                'user_info.avatar'
            )
            ->where('users.email', $email)
            ->first();

        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user->password)) {
            return null;
        }

        return (array)$user;
    }

    public function register(string $fullname, string $email, string $password): bool
    {
        $exists = DB::table('users')->where('email', $email)->exists();
        if ($exists) {
            return false;
        }

        DB::table('users')->insert([
            'fullname' => $fullname,
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => 'user',
            'created_at' => now()
        ]);

        return true;
    }

    public function emailExists(string $email): bool
    {
        return DB::table('users')->where('email', $email)->exists();
    }
}
