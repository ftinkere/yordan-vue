<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyRequest;
use App\Models\User;
use App\Models\VerificationToken;
use App\Services\AuthService;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
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

    public function login(): \Inertia\Response {
        return Inertia::render('Auth/Login');
    }

    public function login_post(LoginRequest $request) {
        $data = $request->validated();

        $isLogged = Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $data['remember']);
        if (!$isLogged) {
            Session::flash('message', ['type' => 'error', 'message' => 'Неправильные почта или пароль']);
            return redirect()->route('login');
        }
        return redirect()->route('main');
    }

    public function logout(LogoutRequest $request): \Illuminate\Http\RedirectResponse {
        Auth::logout();

        return redirect()->route('main');
    }

    public function cabinet() {
        return Inertia::render('Auth/Cabinet');
    }

    public function verify(VerifyRequest $request) {
        [ 'token' => $token ] = $request->validated();

        $token = VerificationToken::whereToken($token)->first();
        if (!$token) {
            Session::flash('message', ['type' => 'error', 'message' => 'Токен не найден']);
            return redirect()->route('main');
        }
        $user = $token->user;
        $token->delete();

        foreach (VerificationToken::whereUserId($user->id)->get() as $token) {
            $token->delete();
        }

        $time = date('Y-m-d H:i:s');
        $user->email_verified_at = $time;
        $user->save();

        Session::flash('message', ['type' => 'success', 'message' => 'Успешно подтверждено']);

        if (Auth::id() == $user->id){
            return redirect()->route('cabinet');
        }
        return redirect()->route('main');
    }
}
