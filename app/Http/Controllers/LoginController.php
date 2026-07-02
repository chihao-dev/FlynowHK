<?php
namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function show()
    {
        return view('login');
    }

    public function handleLogin(Request $request)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email    = trim($request->input('email', ''));
        $password = $request->input('password', '');

        if ($email === '' || $password === '') {
            return view('login', ['err' => 'Vui lòng nhập đầy đủ email và mật khẩu']);
        }

        $user = $this->authService->attemptLogin($email, $password);

        if (!$user) {
            return view('login', ['err' => 'Email hoặc mật khẩu không đúng']);
        }

        $_SESSION['user_id']  = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role']     = $user['role'];
        $_SESSION['avatar']   = $user['avatar'];

        if ($user['role'] === 'admin') {
            return redirect('/admin/dashboard.php');
        }

        return redirect('/');
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        return redirect('/');
    }
}
