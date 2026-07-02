<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function show()
    {
        return view('register');
    }

    public function handleRegister(Request $request)
    {
        $fullname = trim($request->input('fullname', ''));
        $email    = trim($request->input('email', ''));
        $password = $request->input('password', '');

        if ($this->authService->emailExists($email)) {
            return view('register', ['msg' => 'Email này đã được đăng ký.']);
        }

        $success = $this->authService->register($fullname, $email, $password);

        if ($success) {
            return view('register', ['msg' => 'Đăng ký thành công! Hãy đăng nhập.']);
        }

        return view('register', ['msg' => 'Có lỗi xảy ra, vui lòng thử lại.']);
    }
}
