<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function getFullname($userId)
    {
        return DB::table('users')
            ->where('id', $userId)
            ->value('fullname') ?? 'Nguyễn Văn A';
    }

    public function findById($userId)
    {
        return DB::table('users')->where('id', $userId)->first();
    }
}
