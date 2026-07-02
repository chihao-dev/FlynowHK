<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class ProfileService
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getProfile(int $userId): ?array
    {
        $row = DB::table('users')
            ->leftJoin('user_info', 'user_info.user_id', '=', 'users.id')
            ->select(
                'users.email',
                'user_info.fullname',
                'user_info.birthdate',
                'user_info.address',
                'user_info.phone',
                'user_info.avatar'
            )
            ->where('users.id', $userId)
            ->first();

        return $row ? (array)$row : null;
    }

    /**
     * Update profile for a user. Returns true on success.
     * $avatarFile: ['tmp_name' => ..., 'name' => ..., 'error' => ..., 'size' => ...] or null
     */
    public function updateProfile(int $userId, array $postData, ?array $avatarFile = null): bool
    {
        $fullname = trim($postData['fullname'] ?? '');
        if ($fullname === '') {
            return false;
        }

        // Retrieve current avatar
        $current = DB::table('user_info')->where('user_id', $userId)->first();
        $avatar = $current ? $current->avatar : null;

        // Handle avatar upload
        if ($avatarFile && ($avatarFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($avatarFile['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowed) && $avatarFile['size'] <= 2 * 1024 * 1024) {
                $uploadDir = public_path('uploads');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $newName = $uploadDir . "/avatar_{$userId}.{$ext}";
                if (move_uploaded_file($avatarFile['tmp_name'], $newName)) {
                    $avatar = "uploads/avatar_{$userId}.{$ext}";
                }
            }
        }

        $data = [
            'fullname'  => $fullname,
            'birthdate' => $postData['birthdate'] ?: null,
            'address'   => trim($postData['address'] ?? ''),
            'phone'     => trim($postData['phone'] ?? ''),
            'avatar'    => $avatar,
        ];

        $exists = DB::table('user_info')->where('user_id', $userId)->exists();
        if ($exists) {
            DB::table('user_info')->where('user_id', $userId)->update($data);
        } else {
            DB::table('user_info')->insert(array_merge(['user_id' => $userId], $data));
        }

        DB::table('users')->where('id', $userId)->update(['fullname' => $fullname]);

        return true;
    }
}
