<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function register(): \Inertia\Response {
        return Inertia::render('Auth/Register');
    }

    public function register_post(RegisterRequest $request, AuthService $auth, MailService $mail): \Illuminate\Http\RedirectResponse {
        $data = $request->validated();

        $user = $auth->register($data['name'], $data['email'], $data['password']);
        $mail->sendVerificationMail($user);

        return redirect()->route('login');
    }

    public function login() {
        return Inertia::render('Auth/Login');
    }

    public function login_post(LoginRequest $request, AuthService $auth) {
        $data = $request->validated();

        $res = Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $data['remember']);

        return redirect()->route('main');
    }
}
