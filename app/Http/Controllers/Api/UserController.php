<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserUpdateRequest;
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
}
