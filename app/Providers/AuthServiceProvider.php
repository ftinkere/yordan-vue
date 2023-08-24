<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
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
        Gate::define('edit-language', static function (User $user, Language $language) {
            if ($language->user_id == $user->id) {
                return Response::allow();
            } else {
                return Response::deny('You are not owner');
            }
        });
    }
}
