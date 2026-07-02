<?php

namespace App\Http\Controllers;

class AboutController extends Controller
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            return redirect('/admin/dashboard.php');
        }

        return view('about');
    }
}
