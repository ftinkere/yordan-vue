<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\VerifyRequest;
use App\Http\Requests\UserChangePasswordRequest;
use App\Http\Requests\UserPushAvatarRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Models\VerificationToken;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class UserController extends Controller
{

    public function profile($user_id) {
        $user = User::with('languages')->findOrFail($user_id);
        return Inertia::render('UserProfile', compact('user'));
    }

    public function cabinet() {
        return Inertia::render('Auth/Cabinet');
    }

    public function update(Request $request) {
        $data = $request->validate([
            'name' => 'filled',
            'email' => 'email',
        ], messages: [
            'name.filled' => 'Имя не может быть пустым.',
            'email' => 'Введите правильный email.',
        ]);

        $user = Auth::user();
        $old_email = $user->email;

        $user->update($data);

        if ($old_email != $user->email) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('user.cabinet');
    }

    public function push_avatar(Request $request) {
        $request->validate([
            'avatar' => 'required|file|image',
        ], messages: [
            'avatar.required' => 'Изображение обязательно.',
            'avatar.file' => 'Аватар должен быть файлом.',
            'avatar.image' => 'Аватар должен быть изображением.',
        ]);

        $file = $request->file('avatar');
        if ($file !== null) {
            $user = Auth::user();

            $path = $file->storeAs('/avatars', (string)$user->id . '.' . $file->getClientOriginalExtension(), 'public');
            $path = '/storage/' . $path;
            $user->avatar = $path;
            $user->save();
            return redirect()->route('user.cabinet');
        }
        Session::flash('message', ['type' => 'error', 'message' => 'Ошибка загрузки файла.']);
        return redirect()->route('user.cabinet')->withErrors('Ошибка загрузки файла.');
    }

    public function change_password(Request $request) {
        ['password' => $password] = $request->validate([
            'password' => 'required|min:8',
        ], messages: [
            'password.required' => 'Новый пароль не может быть пустым.',
            'password.min' => 'Пароль не может быть меньше 8 символов.',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($password);
        $user->save();

        return redirect()->route('user.cabinet');
    }

    public function resend_confirmation(MailService $mailService) {
        $response = $mailService->sendVerificationMail(Auth::user());
        if ($response) {
            return redirect()->route('user.cabinet');
        }
        Session::flash('message', ['type' => 'error', 'message' => 'Ошибка отправки письма.']);
        return redirect()->route('user.cabinet')->withErrors('Ошибка отправки письма.');
    }


    public function verify(Request $request) {
        [ 'token' => $token ] = $request->validate([
            'token' => 'required'
        ], messages: [
            'token.required' => 'Укажите токен верификации'
        ]);

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
