<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\RobotActivityService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TriggerRobotActivity implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;
    public int $timeout = 60;

    public function __construct(
        private string $activity,
        private int $robotId
    ) {}

    public function handle(RobotActivityService $robotService): void
    {
        $robot = User::find($this->robotId);
        
        if (!$robot || !$robot->is_robot) {
            return;
        }

        $robotService->awardPointsToRobot($robot, $this->activity);
    }
}
