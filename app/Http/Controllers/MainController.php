<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MainController extends Controller
{
    public function index() {
        $lasts = Language::orderBy('updated_at', 'desc')->limit(15)->get();
        $owned = Auth::user()?->languages ?: [];
        return Inertia::render('MainPage', compact('lasts', 'owned'));
    }

    public function info() {
        return Inertia::render('InfoPage');
    }

    public function about() {
        return Inertia::render('AboutPage');
    }
}
