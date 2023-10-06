<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\Grammatic;
use App\Models\GrammaticValue;
use App\Models\Language;
use App\Models\LanguageSound;
use App\Models\Lexeme;
use App\Models\Link;
use App\Models\Orthographeme;
use App\Models\OrthographemePronunciation;
use App\Models\Sound;
use App\Models\User;
use App\Models\Vocabula;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $can_edit = static function (User $user, Language $language) {
            if ($language->user_id == $user->id && $user->email_verified_at !== null) {
                return Response::allow();
            } else {
                return Response::deny('Вы не владелец.');
            }
        };

        Gate::define('edit-language', static function (User $user, Language $language) use ($can_edit) {
            return $can_edit($user, $language);
        });

        Gate::define('add-language', static function (User $user) {
            if ($user->email_verified_at !== null) {
                return Response::allow();
            } else {
                return Response::deny('Ваша почта не подтверждена.');
            }
        });

        Gate::define('article-is-language', static function (User $user, Article $article) use ($can_edit) {
            return $can_edit($user, $article->language);
        });

        Gate::define('tag-is-language', static function (User $user, ArticleTag $tag) use ($can_edit) {
            return $can_edit($user, $tag->article->language);
        });

        Gate::define('grammatic-is-language', static function (User $user, Grammatic $grammatic) use ($can_edit) {
            return $can_edit($user, $grammatic->language);
        });

        Gate::define('grammatic-value-is-language', static function (User $user, GrammaticValue $value) use ($can_edit) {
            return $can_edit($user, $value->grammatic->language);
        });

        Gate::define('vocabula-is-language', static function (User $user, Vocabula $vocabula) use ($can_edit) {
            return $can_edit($user, $vocabula->language);
        });

        Gate::define('lexeme-is-language', static function (User $user, Lexeme $lexeme) use ($can_edit) {
            return $can_edit($user, $lexeme->vocabula->language);
        });

        Gate::define('link-is-language', static function (User $user, Link $link) use ($can_edit) {
            return $can_edit($user, $link->from->vocabula->language);
        });

        Gate::define('language_sound-is-language', static function (User $user, LanguageSound $language_sound) use ($can_edit) {
            return $can_edit($user, $language_sound->language);
        });

        Gate::define('sound-is-language', static function (User $user, Sound $sound) use ($can_edit) {
            if ($sound->language_id === null) {
                return Response::deny('Вы не владелец.');
            }
            return $can_edit($user, $sound->language);
        });

        Gate::define('orthographeme-is-language', static function (User $user, Orthographeme $orthographeme) use ($can_edit) {
            return $can_edit($user, $orthographeme->language);
        });

        Gate::define('pronunciation-is-language', static function (User $user, OrthographemePronunciation $pronunciation) use ($can_edit) {
            return $can_edit($user, $pronunciation->orthographeme->language);
        });
    }
}
