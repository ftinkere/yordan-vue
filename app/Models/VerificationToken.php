<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Hash;
use Nette\Utils\Random;

/**
 * App\Models\VerificationToken
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string|null $verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationToken whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerificationToken whereVerifiedAt($value)
 * @mixin \Eloquent
 */
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
