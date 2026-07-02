<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Seed admin user
        $this->userService->seedAdminUser();

        // 2. Check admin session and redirect if admin
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            return redirect('/admin/dashboard.php');
        }

        return view('index');
    }
}
