<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Services\RobotActivityService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Login;

class RobotEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            'App\Listeners\TriggerRobotActivityOnRegistration',
        ],
        Login::class => [
            'App\Listeners\TriggerRobotActivityOnLogin',
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
