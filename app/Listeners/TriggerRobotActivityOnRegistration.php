<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use App\Services\RobotActivityService;

class TriggerRobotActivityOnRegistration
{
    private RobotActivityService $robotService;

    public function __construct(RobotActivityService $robotService)
    {
        $this->robotService = $robotService;
    }

    public function handle(Registered $event): void
    {
        if (!$event->user->is_robot) {
            $this->robotService->handleUserActivity('user_signup');
            $this->robotService->markRealUserActivity();
        }
    }
}
