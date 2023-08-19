<?php

namespace App\Mail;

class VerifyMail extends \Illuminate\Mail\Mailable
{
    protected $token;

    public function __construct($token) {
        $this->token = $token;
    }

    public function build() {
        return $this->view('mails.verify')
            ->subject('Ёрдан. Подтверждение почты.')
            ->with(['token' => $this->token]);
    }
}
