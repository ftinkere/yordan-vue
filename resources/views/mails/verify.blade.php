<?php
/**
 * @var \App\Models\VerificationToken $token
 */
?>

<div>
    <p>
        Подтвердите свою почту, {{ $token->user->name }}.
        <a href="{{ route('verify', ['token' => $token->token]) }}">Подтвердить</a>
    </p>
</div>
