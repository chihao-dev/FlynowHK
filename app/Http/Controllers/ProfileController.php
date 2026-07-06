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

    /**
     * Compatibility method for public/profile.php
     */
    public function getProfile($userId)
    {
        return $this->profileService->getProfile($userId);
    }

    /**
     * Compatibility method for public/profile.php
     */
    public function updateProfile($userId, $data, $files)
    {
        $avatarFile = isset($files['avatar']) && $files['avatar']['error'] === UPLOAD_ERR_OK ? $files['avatar'] : null;
        return $this->profileService->updateProfile($userId, $data, $avatarFile);
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
            return redirect()->route('profile')->with('success', 'Cập nhật thành công!');
        }

        return redirect()->back()->withErrors(['msg' => 'Cập nhật thất bại.']);
    }
}
