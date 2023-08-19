<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Hash;
use Nette\Utils\Random;

class VerificationToken extends Model
{
    use HasFactory;

    protected $table = 'verification_tokens';

    public static function new(User $user) {
        $token = new self();
        $token->user_id = $user->id;
        $token->token = Hash::make(Random::generate(32));
        if ($token->save()) {
            return $token;
        }
        return false;
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
