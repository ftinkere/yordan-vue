<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;
use Nette\Utils\Random;

/**
 * App\Models\PasswordResetToken
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordResetToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordResetToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordResetToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordResetToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordResetToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordResetToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordResetToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordResetToken whereUserId($value)
 * @mixin \Eloquent
 */
class PasswordResetToken extends Model
{
    use HasFactory;

    protected $table = 'password_reset_tokens';

    protected $fillable = ['user_id', 'token'];

    public static function new(User $user): self {
        $token = new self();
        $token->user_id = $user->id;
        $token->token = Hash::make(Random::generate(32));
        if ($token->save()) {
            return $token;
        }
        $token->save();
        return $token;
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
