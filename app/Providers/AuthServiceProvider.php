<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\Language;
use App\Models\User;
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
    }
}
