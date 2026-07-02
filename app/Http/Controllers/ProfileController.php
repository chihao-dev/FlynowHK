<?php

namespace App\Http\Controllers;

use App\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function show(Request $request)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            return redirect('/login.php');
        }

        $userId = $_SESSION['user_id'];
        $user   = $this->profileService->getProfile($userId);

        return view('profile', [
            'user'   => $user,
            'msg'    => '',
            'errors' => []
        ]);
    }

    public function update(Request $request)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            return redirect('/login.php');
        }

        $userId    = $_SESSION['user_id'];
        $avatarFile = $request->hasFile('avatar')
            ? [
                'tmp_name' => $request->file('avatar')->getRealPath(),
                'name'     => $request->file('avatar')->getClientOriginalName(),
                'error'    => UPLOAD_ERR_OK,
                'size'     => $request->file('avatar')->getSize(),
              ]
            : null;

        $success = $this->profileService->updateProfile($userId, $request->all(), $avatarFile);

        if ($success) {
            $updatedUser = $this->profileService->getProfile($userId);
            $_SESSION['fullname'] = $updatedUser['fullname'];
            $_SESSION['avatar']   = $updatedUser['avatar'];
            return redirect('/profile.php');
        }

        return view('profile', [
            'user'   => $this->profileService->getProfile($userId),
            'msg'    => '',
            'errors' => ['Cập nhật thất bại.']
        ]);
    }
}
