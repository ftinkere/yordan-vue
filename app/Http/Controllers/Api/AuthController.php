<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LogoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
    function login(LoginRequest $request) {
        $data = $request->validated();
        $remember = $data['remember'];
        unset($data['remember']);
        return Auth::attempt($data, $remember);
    }

    function logout(LogoutRequest $request) {
        Auth::logout();

        return true;
    }
}
