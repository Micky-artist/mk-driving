<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RobotActivityService;

class RunRobotActivity extends Command
{
    protected $signature = 'robots:trigger {activity : The user activity that triggers robot response}';
    protected $description = 'Trigger robot activity based on user events';

    public function handle(RobotActivityService $robotService): int
    {
        $activity = $this->argument('activity');
        
        $this->info("Triggering robot activity for: {$activity}");
        
        $robotService->handleUserActivity($activity);
        
        $this->info('Robot activity triggered successfully.');
        return 0;
    }
}
