<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function profile($user_id) {
        $user = User::with('languages')->findOrFail($user_id);
        return Inertia::render('UserProfile', compact('user'));
    }
}
