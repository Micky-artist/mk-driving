<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\RobotActivityService;

class TriggerRobotActivityOnLogin
{
    private RobotActivityService $robotService;

    public function __construct(RobotActivityService $robotService)
    {
        $this->robotService = $robotService;
    }

    public function handle(Login $event): void
    {
        if (!$event->user->is_robot) {
            $this->robotService->handleUserActivity('user_login');
            $this->robotService->markRealUserActivity();
        }
    }
}
