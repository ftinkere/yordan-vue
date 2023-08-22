<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserPushAvatarRequest;
use App\Http\Requests\Api\UserUpdateRequest;
use App\Http\Requests\Auth\VerifyRequest;
use App\Models\VerificationToken;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function update(UserUpdateRequest $request) {
        $data = $request->validated();

        $user = Auth::user();
        $user->update($data);

        return $user->toJson();
    }

    public function resend_confirmation(MailService $mailService) {
        $response = $mailService->sendVerificationMail(Auth::user());
        if ($response) {
            return ['message' => 'Successful'];
        }
        return ['message' => 'Error'];
    }

    public function push_avatar(UserPushAvatarRequest $request) {
        $request->validated();
        $file = $request->file('avatar');
        if ($file != null) {
            $user = Auth::user();

            $path = $file->storeAs('/avatars', (string)$user->id . '.' . $file->getClientOriginalExtension(), 'public');
            $path = '/storage/' . $path;
            $user->avatar = $path;
            $user->save();
            return ['message' => 'Success', 'path' => $path];
        }
        return ['message' => 'Error'];
    }
}
