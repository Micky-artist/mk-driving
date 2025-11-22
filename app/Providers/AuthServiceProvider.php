<?php

namespace App\Providers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Policies\QuizPolicy;
use App\Policies\QuizAttemptPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Quiz::class => QuizPolicy::class,
        QuizAttempt::class => QuizAttemptPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define admin role gate
        Gate::define('isAdmin', function ($user) {
            return $user->hasRole('admin');
        });

        // Define content creator role gate
        Gate::define('isContentCreator', function ($user) {
            return $user->hasRole('content_creator');
        });

        // Define student role gate
        Gate::define('isStudent', function ($user) {
            return $user->hasRole('student');
        });
    }
}
