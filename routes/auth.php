<?php


use App\Http\Controllers\AuthController;
use App\Http\Middleware\HasRoleTeacherChecker;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::group(['middleware' => HasRoleTeacherChecker::class], function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::any('/logout', [AuthController::class, 'logout'])->name('logout');
