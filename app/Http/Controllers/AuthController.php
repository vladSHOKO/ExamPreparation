<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('login', 'password');

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended();
        }

        return back()->withErrors([
            'login' => 'Неверный логин или пароль'
        ])->withInput();
    }

    public function showRegisterForm()
    {
        $teachers = Teacher::with('user')->get();

        return view('auth.register', ['teachers' => $teachers]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'login' => 'required|string|unique:users',
            'password' => 'required|string|confirmed',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $user = new User();
        $user->login = $request->get('login');
        $user->password = Hash::make($request->get('password'));
        $user->save();

        $student = new Student();
        $student->teacher_id = $request->get('teacher_id');
        $student->user_id = $user->id;
        $student->save();

        return redirect()->intended();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->intended();
    }
}
