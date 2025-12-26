<?php

namespace App\Console\Commands;

use App\Models\Leaderboard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateLeaderboardCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard:update {type=weekly}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update leaderboard rankings for specified type (weekly, monthly, all-time)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->argument('type');
        
        try {
            $this->info("Updating {$type} leaderboard...");
            
            switch ($type) {
                case 'weekly':
                    $leaderboard = Leaderboard::getWeekly();
                    break;
                case 'monthly':
                    $leaderboard = Leaderboard::getMonthly();
                    break;
                case 'all-time':
                    $leaderboard = Leaderboard::getAllTime();
                    break;
                default:
                    $this->error("Invalid leaderboard type: {$type}");
                    $this->info('Available types: weekly, monthly, all-time');
                    return Command::FAILURE;
            }
            
            $startTime = microtime(true);
            $leaderboard->updateRankings();
            $endTime = microtime(true);
            
            $executionTime = round(($endTime - $startTime) * 1000, 2);
            
            $this->info("{$type} leaderboard updated successfully in {$executionTime}ms");
            
            Log::info("Leaderboard updated", [
                'type' => $type,
                'execution_time_ms' => $executionTime,
                'updated_at' => now()
            ]);
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("Failed to update {$type} leaderboard: " . $e->getMessage());
            
            Log::error("Leaderboard update failed", [
                'type' => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }
}
