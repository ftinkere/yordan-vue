<?php

namespace App\Services;

use App\Mail\VerifyMail;
use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function sendVerificationMail(User $user) {
        $token = VerificationToken::new($user);

        return Mail::to($user->email)->send(new VerifyMail($token));
    }
}
