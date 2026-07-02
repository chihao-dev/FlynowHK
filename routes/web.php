<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheapTicketController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\MyTicketController;
use App\Http\Controllers\AirportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// API
Route::get('/api/airports/search', [AirportController::class, 'search'])->name('api.airports.search');

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/index.php', [HomeController::class, 'index']);

// Auth
Route::get('/login.php', [LoginController::class, 'show'])->name('login');
Route::post('/login.php', [LoginController::class, 'handleLogin']);
Route::get('/logout.php', [LoginController::class, 'logout'])->name('logout');

Route::get('/register.php', [RegisterController::class, 'show'])->name('register');
Route::post('/register.php', [RegisterController::class, 'handleRegister']);

// Profile
Route::get('/profile.php', [ProfileController::class, 'show'])->name('profile');
Route::post('/profile.php', [ProfileController::class, 'update']);

// Flight search
Route::get('/cheap-tickets.php', [CheapTicketController::class, 'index'])->name('cheap-tickets');
Route::post('/cheap-tickets.php', [CheapTicketController::class, 'index']);

// Checkout
Route::get('/checkout.php', [CheckoutController::class, 'showCheckout'])->name('checkout');
Route::post('/checkout.php', [CheckoutController::class, 'handlePost']);

// Promotions
Route::get('/promotions.php', [PromotionController::class, 'index'])->name('promotions');

// Static pages
Route::get('/about.php', [AboutController::class, 'index'])->name('about');
Route::get('/guide.php', [GuideController::class, 'index'])->name('guide');

// My tickets
Route::get('/my-tickets.php', [MyTicketController::class, 'index'])->name('my-tickets');
Route::get('/ticket-detail.php', [MyTicketController::class, 'show'])->name('ticket-detail');
